<?php

namespace Targetforce\Base\Pipelines\Posts;

use Targetforce\Base\Events\MessageDispatchEvent;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Tag;

class CreateMessages
{
    /**
     * Stores unique subscribers for this post
     *
     * @var array
     */
    protected $sentItems = [];

    /**
     * CreateMessages handler
     *
     * @param Post $post
     * @param $next
     * @return Post
     * @throws \Exception
     */
    public function handle(Post $post, $next)
    {
        if ($post->send_to_all) {
            $this->handleAllSubscribers($post);
        } else {
            $this->handleTags($post);
        }

        return $next($post);
    }

    /**
     * Handle a post where all subscribers have been selected
     *
     * @param Post $post
     * @throws \Exception
     */
    protected function handleAllSubscribers(Post $post)
    {
        Subscriber::where('workspace_id', $post->workspace_id)
            ->whereNull('unsubscribed_at')
            ->chunkById(1000, function ($subscribers) use ($post) {
                $this->dispatchToSubscriber($post, $subscribers);
            }, 'id');
    }

    /**
     * Loop through each tag
     *
     * @param Post $post
     */
    protected function handleTags(Post $post)
    {
        foreach ($post->tags as $tag) {
            $this->handleTag($post, $tag);
        }
    }

    /**
     * Handle each tag
     *
     * @param Post $post
     * @param Tag $tag
     *
     * @return void
     */
    protected function handleTag(Post $post, Tag $tag): void
    {
        \Log::info('- Handling Post Tag id='.$tag->id);

        $tag->subscribers()->whereNull('unsubscribed_at')->chunkById(1000, function ($subscribers) use ($post) {
            $this->dispatchToSubscriber($post, $subscribers);
        }, 'targetforce_subscribers.id');
    }

    /**
     * Dispatch the post to a given subscriber
     *
     * @param Post $post
     * @param $subscribers
     */
    protected function dispatchToSubscriber(Post $post, $subscribers)
    {
        \Log::info('- Number of subscribers in this chunk: ' . count($subscribers));

        foreach ($subscribers as $subscriber) {
            if (! $this->canSendToSubscriber($post->id, $subscriber->id)) {
                continue;
            }

            $this->dispatch($post, $subscriber);
        }
    }

    /**
     * Check if we can send to this subscriber
     * @todo check how this would impact on memory with 200k subscribers?
     *
     * @param int $postId
     * @param int $subscriberId
     *
     * @return bool
     */
    protected function canSendToSubscriber($postId, $subscriberId): bool
    {
        $key = $postId . '-' . $subscriberId;

        if (in_array($key, $this->sentItems, true)) {
            \Log::info('- Subscriber has already been sent a message post_id=' . $postId . ' subscriber_id=' . $subscriberId);

            return false;
        }

        $this->appendSentItem($key);

        return true;
    }

    /**
     * Append a value to the sentItems
     *
     * @param string $value
     * @return void
     */
    protected function appendSentItem(string $value): void
    {
        $this->sentItems[] = $value;
    }

    /**
     * Dispatch the message
     *
     * @param Post $post
     * @param Subscriber $subscriber
     */
    protected function dispatch(Post $post, Subscriber $subscriber): void
    {
        if ($post->save_as_draft) {
            $this->saveAsDraft($post, $subscriber);
        } else {
            $this->dispatchNow($post, $subscriber);
        }
    }

    /**
     * Dispatch a message now
     *
     * @param Post $post
     * @param Subscriber $subscriber
     * @return Message
     */
    protected function dispatchNow(Post $post, Subscriber $subscriber): Message
    {
        // If a message already exists, then we're going to assume that
        // it has already been dispatched. This makes the dispatch fault-tolerant
        // and prevent dispatching the same message to the same subscriber
        // more than once
        if ($message = $this->findMessage($post, $subscriber)) {
            \Log::info('Message has previously been created post=' . $post->id . ' subscriber=' . $subscriber->id);

            return $message;
        }

        // the message doesn't exist, so we'll create and dispatch
        \Log::info('Saving empty email message post=' . $post->id . ' subscriber=' . $subscriber->id);
        $attributes = [
            'workspace_id' => $post->workspace_id,
            'subscriber_id' => $subscriber->id,
            'source_type' => Post::class,
            'source_id' => $post->id,
            'recipient_email' => $subscriber->email,
            'subject' => $post->subject,
            'from_name' => $post->from_name,
            'from_email' => $post->from_email,
            'queued_at' => null,
            'sent_at' => null,
        ];

        $message = new Message($attributes);
        $message->save();

        event(new MessageDispatchEvent($message));

        return $message;
    }

    /**
     * @param Post $post
     * @param Subscriber $subscriber
     */
    protected function saveAsDraft(Post $post, Subscriber $subscriber)
    {
        \Log::info('Saving message as draft post=' . $post->id . ' subscriber=' . $subscriber->id);

        Message::firstOrCreate(
            [
                'workspace_id' => $post->workspace_id,
                'subscriber_id' => $subscriber->id,
                'source_type' => Post::class,
                'source_id' => $post->id,
            ],
            [
                'recipient_email' => $subscriber->email,
                'subject' => $post->subject,
                'from_name' => $post->from_name,
                'from_email' => $post->from_email,
                'queued_at' => now(),
                'sent_at' => null,
            ]
        );
    }

    protected function findMessage(Post $post, Subscriber $subscriber): ?Message
    {
        return Message::where('workspace_id', $post->workspace_id)
            ->where('subscriber_id', $subscriber->id)
            ->where('source_type', Post::class)
            ->where('source_id', $post->id)
            ->first();
    }
}
