<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Models\Post;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Models\Subscriber;
use Targetforce\Base\Models\Template;
use Targetforce\Base\Services\Content\MergeContentService;
use Tests\TestCase;

class MergeContentTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function post_content_can_be_merged()
    {
        // given
        $content = $this->faker->sentence;
        $message = $this->generatePostMessage($content);

        // when
        $mergedContent = $this->mergeContent($message);

        // then
        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>' . $content . '</p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function it_can_handle_a_null_value_for_post_content()
    {
        $content = null;
        $message = $this->generatePostMessage($content, '<p>Hello this is some {{content}}</p>');

        $mergedContent = $this->mergeContent($message);

        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hello this is some </p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function the_unsubscribe_url_tag_is_replaced_with_a_valid_unsubscribe_link()
    {
        // given
        $message = $this->generatePostMessage('<a href="{{ unsubscribe_url }}">Unsubscribe Here</a>');

        // when
        $mergedContent = $this->mergeContent($message);

        // then
        $route = route('targetforce.subscriptions.unsubscribe', $message->hash);

        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p><a href="' . $route . '">Unsubscribe Here</a></p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function the_email_tag_is_replaced_with_the_subscriber_email()
    {
        // given
        $message = $this->generatePostMessage('Hi, {{ email }}');

        // when
        $mergedContent = $this->mergeContent($message);

        // then
        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hi, ' . $message->recipient_email . '</p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function the_first_name_tag_is_replaced_with_the_subscriber_first_name()
    {
        // given
        $message = $this->generatePostMessage('Hi, {{ first_name }}');

        // when
        $mergedContent = $this->mergeContent($message);

        // then
        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hi, ' . $message->subscriber->first_name . '</p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function first_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_first_name_is_null()
    {
        $message = $this->generatePostMessage('Hi, {{ first_name }}');

        $message->subscriber()->associate(Subscriber::factory()->create([
            'first_name' => null,
            'last_name' => $this->faker->lastName
        ]));

        // when
        $mergedContent = $this->mergeContent($message);

        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hi, </p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function the_last_name_tag_is_replaced_with_the_subscriber_last_name()
    {
        // given
        /** @var Workspace $workspace */
        $message = $this->generatePostMessage('Hi, {{ last_name }}');

        // when
        $mergedContent = $this->mergeContent($message);

        // then
        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hi, ' . $message->subscriber->last_name . '</p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    /** @test */
    public function last_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_last_name_is_null()
    {
        $message = $this->generatePostMessage('Hi, {{ last_name }}');

        $message->subscriber()->associate(Subscriber::factory()->create([
            'first_name' => $this->faker->firstName,
            'last_name' => null
        ]));

        // when
        $mergedContent = $this->mergeContent($message);

        // NOTE(david): the string has to be formatted like this to match!
        $expectedHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><body><p>Hi, </p></body></html>';

        self::assertEquals($expectedHtml, $mergedContent);
    }

    private function generatePostMessage(?string $postContent, ?string $templateContent = null): Message
    {
        /** @var Template $template */
        $template = Template::factory()->create([
            'content' => $templateContent ?? '<p>{{content}}</p>',
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        /** @var Post $post */
        $post = Post::factory()->create([
            'template_id' => $template->id,
            'content' => $postContent,
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);

        return Message::factory()->create([
            'source_type' => Post::class,
            'source_id' => $post->id,
            'workspace_id' => Targetforce::currentWorkspaceId()
        ]);
    }

    private function mergeContent(Message $message): string
    {
        /** @var MergeContentService $mergeContent */
        $mergeContent = app(MergeContentService::class);

        return $mergeContent->handle($message);
    }
}
