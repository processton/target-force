<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Services\Content\MergeSubjectService;
use Tests\TestCase;

class MergeSubjectTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function the_email_tag_is_replaced_with_the_subscriber_email()
    {
        // given
        $subject = 'Hi, {{email}}';
        $message = $this->generatePostMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo@bar.com', $mergedSubject);
    }

    /** @test */
    public function the_first_name_tag_is_replaced_with_the_subscriber_first_name()
    {
        // given
        $subject = 'Hi, {{first_name}}';
        $message = $this->generatePostMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo', $mergedSubject);
    }

    /** @test */
    public function the_first_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_first_name_is_null()
    {
        // given
        $subject = 'Hi, {{first_name}}';
        $message = $this->generatePostMessage($subject, 'foo@bar.com');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, ', $mergedSubject);
    }

    /** @test */
    public function the_last_name_tag_is_replaced_with_the_subscriber_last_name()
    {
        // given
        $subject = 'Hi, {{last_name}}';
        $message = $this->generatePostMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, bar', $mergedSubject);
    }

    /** @test */
    public function the_last_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_last_name_is_null()
    {
        // given
        $subject = 'Hi, {{last_name}}';
        $message = $this->generatePostMessage($subject, 'foo@bar.com');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, ', $mergedSubject);
    }

    /** @test */
    public function multiple_different_tags_are_replaced()
    {
        // given
        $subject = 'Hi, {{first_name}} {{ last_name }} ({{email }})';
        $message = $this->generatePostMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo bar (foo@bar.com)', $mergedSubject);
    }

    private function generatePostMessage(
        string $postSubject,
        string $email,
        ?string $firstName = null,
        ?string $lastName = null
    ): Message {
        /** @var Post $post */
        $post = Post::factory()->create([
            'content' => '<p>Content</p>',
            'subject' => $postSubject,
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        return Message::factory()->create([
            'workspace_id' => Targetforce::currentWorkspaceId(),
            'subscriber_id' => $subscriber->id,
            'source_type' => Post::class,
            'source_id' => $post->id,
            'subject' => $postSubject,
            'recipient_email' => $email,
        ]);
    }

    private function mergeSubject(Message $message): string
    {
        /** @var MergeSubjectService $mergeSubject */
        $mergeSubject = app(MergeSubjectService::class);

        return $mergeSubject->handle($message);
    }
}
