<?php

namespace App\Services;

use App\Models\TestRequest;
use App\Models\User;
use App\Notifications\WorkflowActivityNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WorkflowNotificationService
{
    /** Roles that always receive every workflow notification. */
    private const ALWAYS_NOTIFY_ROLES = ['admin'];

    /**
     * @param  array<string, mixed>  $meta
     */
    public function notifyLab(string $type, TestRequest $testRequest, ?User $actor = null, array $meta = []): void
    {
        $this->notify(['lab'], $type, $testRequest, $actor, $meta);
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function notifyReception(string $type, TestRequest $testRequest, ?User $actor = null, array $meta = []): void
    {
        $this->notify(['receptionist'], $type, $testRequest, $actor, $meta);
    }

    /**
     * @param  list<string>  $targetRoles
     * @param  array<string, mixed>  $meta
     */
    private function notify(
        array $targetRoles,
        string $type,
        TestRequest $testRequest,
        ?User $actor,
        array $meta,
    ): void {
        try {
            $recipients = $this->recipientsFor($targetRoles);

            foreach ($recipients as $user) {
                if ($this->shouldSkipRecipient($user, $actor)) {
                    continue;
                }

                try {
                    $user->notify(new WorkflowActivityNotification(
                        $type,
                        $testRequest,
                        $actor?->name,
                        $meta
                    ));
                } catch (\Throwable $e) {
                    Log::error('Workflow notification failed for user', [
                        'user_id' => $user->id,
                        'type' => $type,
                        'test_request_id' => $testRequest->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Workflow notification failed', [
                'type' => $type,
                'target_roles' => $targetRoles,
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  list<string>  $targetRoles
     * @return Collection<int, User>
     */
    private function recipientsFor(array $targetRoles): Collection
    {
        $roles = array_values(array_unique([...$targetRoles, ...self::ALWAYS_NOTIFY_ROLES]));

        return User::query()
            ->whereIn('role', $roles)
            ->get()
            ->unique('id')
            ->values();
    }

    private function shouldSkipRecipient(User $recipient, ?User $actor): bool
    {
        if (! $actor || (int) $recipient->id !== (int) $actor->id) {
            return false;
        }

        return ! $recipient->isAdmin();
    }
}
