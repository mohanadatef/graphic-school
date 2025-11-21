<?php

namespace Modules\Core\Notification\Presentation\Http\Controllers;

use App\Support\Controllers\BaseController;
use Modules\Core\Notification\Application\UseCases\SendNotificationUseCase;
use Modules\Core\Notification\Application\DTOs\SendNotificationDTO;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function send(Request $request, SendNotificationUseCase $useCase)
    {
        $request->validate([
            'channel' => 'required|string|in:email,sms,push',
            'recipient' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'data' => 'sometimes|array',
        ]);

        $dto = SendNotificationDTO::fromArray([
            'channel' => $request->input('channel'),
            'recipient' => $request->input('recipient'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'data' => $request->input('data'),
            'userId' => auth()->id(),
        ]);

        $result = $useCase->execute($dto);

        return $this->success(['sent' => $result], 'Notification sent successfully');
    }
}

