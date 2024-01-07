<?php

declare(strict_types=1);

namespace Targetforce\Base\Services\Messages;

use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;

class MessageTrackingOptions
{
    /** @var bool */
    private $isOpenTracking = true;

    /** @var bool */
    private $isClickTracking = true;

    public static function fromMessage(Message $message): MessageTrackingOptions
    {
        // NOTE(david): at the moment only posts have the ability to turn off tracking, so we start
        // by creating a default set of options that has the tracking on, and only look to adjust that
        // if the message we've got is for a post.
        $trackingOptions = new static;

        if ($message->source && get_class($message->source) === Post::class) {
            return static::fromPost($message->source);
        }

        return $trackingOptions;
    }

    public static function fromPost(Post $post): MessageTrackingOptions
    {
        return (new static)
            ->setIsOpenTracking($post->is_open_tracking ?? true)
            ->setIsClickTracking($post->is_click_tracking ?? true);
    }

    public function isOpenTracking(): bool
    {
        return $this->isOpenTracking;
    }

    public function setIsOpenTracking(bool $isOpenTracking): self
    {
        $this->isOpenTracking = $isOpenTracking;

        return $this;
    }

    public function isClickTracking(): bool
    {
        return $this->isClickTracking;
    }

    public function setIsClickTracking(bool $isClickTracking): self
    {
        $this->isClickTracking = $isClickTracking;

        return $this;
    }

    public function disable(): self
    {
        $this->isOpenTracking = false;
        $this->isClickTracking = false;

        return $this;
    }
}
