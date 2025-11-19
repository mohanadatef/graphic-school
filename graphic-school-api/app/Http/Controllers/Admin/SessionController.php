<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Session\ListSessionRequest;
use App\Http\Requests\Admin\Session\UpdateSessionRequest;
use App\Http\Resources\SessionResource;
use App\Models\Session;
use App\Services\SessionService;

class SessionController extends Controller
{
    public function __construct(private SessionService $sessionService)
    {
    }

    public function index(ListSessionRequest $request)
    {
        $sessions = $this->sessionService->paginate(
            $request->validated(),
            $request->integer('per_page', 10)
        );

        return SessionResource::collection($sessions);
    }

    public function show(Session $session)
    {
        return SessionResource::make($this->sessionService->show($session));
    }

    public function update(UpdateSessionRequest $request, Session $session)
    {
        $session = $this->sessionService->update($session, $request->validated());

        return SessionResource::make($session);
    }

    public function destroy(Session $session)
    {
        $this->sessionService->delete($session);

        return response()->json(['message' => 'Deleted']);
    }
}
