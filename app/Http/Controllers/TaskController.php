<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Requests\TaskUpdateStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected TaskService $taskService) {}

    public function index(): ResourceCollection
    {
        $tasks = Task::with(['client', 'user'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('deadline')
            ->paginate(15);

        return TaskResource::collection($tasks);
    }

    /**
     * @param TaskStoreRequest $request
     * @return TAskResource
     */
    public function store(TaskStoreRequest $request): TaskResource
    {
        $task = $this->taskService->createTask($request->all(), $request->user());
        return TaskResource::make($task);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(TaskUpdateRequest $request, Task $task): ?JsonResponse
    {

        $this->authorize('update', $task);
        try {
            $updatedTask = $this->taskService->updateTask($task, $request->all());

            return response()->json([
                'message' => 'Успешно обновлено',
                'task' => $updatedTask
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Произошла ошибка',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function updateStatus(TaskUpdateStatusRequest $request, Task $task): ?JsonResponse
    {
        $this->authorize('update', $task);
        try {
            $updatedTask = $this->taskService->updateStatus($task, $request->status);
            return response()->json([
                'message' => 'Статус обновлен',
                'task' => $updatedTask
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function today(): ResourceCollection
    {
        $tasks = auth()->user()->tasks()
            ->whereDate('deadline', Carbon::today())
            ->where('status', '!=', 'done')
            ->orderBy('deadline')
            ->get();

        return TaskResource::collection($tasks);
    }

    public function overdue(): ResourceCollection
    {
        $tasks = auth()->user()->tasks()
            ->where('deadline', '<', Carbon::now())
            ->where('status', '!=', 'done')
            ->where('status', '!=', 'cancelled')
            ->orderBy('deadline', 'desc')
            ->get();

        return TaskResource::collection($tasks);
    }

    public function byClient($clientId): ResourceCollection
    {
        $tasks = Task::with('client')
            ->where('client_id', $clientId)
            ->where('user_id', auth()->user()->id)
            ->get();

        return TaskResource::collection($tasks);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Task $task): Response
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->noContent();
    }
}
