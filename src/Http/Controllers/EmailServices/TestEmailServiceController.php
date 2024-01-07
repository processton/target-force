<?php


namespace Targetforce\Base\Http\Controllers\EmailServices;

use Exception;
use Illuminate\Http\RedirectResponse;
use Targetforce\Base\Facades\Targetforce;
use Targetforce\Base\Http\Controllers\Controller;
use Targetforce\Base\Http\Requests\EmailServiceTestRequest;
use Targetforce\Base\Repositories\EmailServiceTenantRepository;
use Targetforce\Base\Services\Messages\DispatchTestMessage;
use Targetforce\Base\Services\Messages\MessageOptions;

class TestEmailServiceController extends Controller
{
    /** @var EmailServiceTenantRepository */
    private $emailServices;

    public function __construct(EmailServiceTenantRepository $emailServices)
    {
        $this->emailServices = $emailServices;
    }

    public function create(int $emailServiceId)
    {
        $emailService = $this->emailServices->find(Targetforce::currentWorkspaceId(), $emailServiceId);

        return view('targetforce::email_services.test', compact('emailService'));
    }

    /**
     * @throws Exception
     */
    public function store(int $emailServiceId, EmailServiceTestRequest $request, DispatchTestMessage $dispatchTestMessage): RedirectResponse
    {
        $emailService = $this->emailServices->find(Targetforce::currentWorkspaceId(), $emailServiceId);

        $options = new MessageOptions();
        $options->setFromEmail($request->input('from'));
        $options->setSubject($request->input('subject'));
        $options->setTo($request->input('to'));
        $options->setBody($request->input('body'));

        try {
            $messageId = $dispatchTestMessage->testService(Targetforce::currentWorkspaceId(), $emailService, $options);

            if (!$messageId) {
                return redirect()
                    ->back()
                    ->with(['error', __('Failed to dispatch test email.')]);
            }

            return redirect()
                ->route('targetforce.email_services.index')
                ->with(['success' => __('The test email has been dispatched.')]);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Response: ' . $e->getMessage());
        }
    }
}
