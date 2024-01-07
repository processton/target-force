<?php

declare(strict_types=1);

namespace Targetforce\Base\Http\Controllers\Workspaces;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Models\Invitation;
use Targetforce\Base\Services\Workspaces\AcceptInvitation;

class PendingInvitationController extends Controller
{
    /** @var AcceptInvitation */
    protected $acceptInvitation;

    public function __construct(AcceptInvitation $acceptInvitation)
    {
        $this->acceptInvitation = $acceptInvitation;
    }

    /**
     * @throws Exception
     */
    public function accept(Request $request, Invitation $invitation): RedirectResponse
    {
        abort_unless($request->user()->id === $invitation->user_id, 404);

        if ($invitation->isExpired()) {
            return redirect()->back()->with('error', __('The invitation is no longer valid.'));
        }

        $this->acceptInvitation->handle($request->user(), $invitation);

        return redirect()->route('targetforce.workspaces.index');
    }

    /**
     * @throws Exception
     */
    public function reject(Request $request, Invitation $invitation): RedirectResponse
    {
        abort_unless($request->user()->id === $invitation->user_id, 404);

        $invitation->delete();

        return redirect()->route('targetforce.workspaces.index');
    }
}
