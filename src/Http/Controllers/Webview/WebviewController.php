<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Webview;

use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\Message;
use Targetforce\Base\Services\Content\MergeContentService;

class WebviewController extends Controller
{
    /** @var MergeContentService */
    private $merger;

    public function __construct(MergeContentService $merger)
    {
        $this->merger = $merger;
    }

    /**
     * @throws Exception
     */
    public function show(string $messageHash): ViewContract
    {
        /** @var Message $message */
        $message = Message::with('subscriber')->where('hash', $messageHash)->first();

        $content = $this->merger->handle($message);

        return view('targetforce::webview.show', compact('content'));
    }
}
