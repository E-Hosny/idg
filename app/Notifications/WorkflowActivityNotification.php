<?php

namespace App\Notifications;

use App\Models\TestRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public TestRequest $testRequest,
        public ?string $actorName = null,
        public array $meta = [],
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable instanceof User
            && $notifiable->receivesWorkflowNotifications()
            && $notifiable->hasDeliverableNotificationEmail()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable instanceof User ? $notifiable->preferredLocale() : 'en';
        $params = $this->translationParams();
        $body = __('notifications.'.$this->type, $params, $locale);
        $subject = 'IDG: '.$body;

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.workflow-notification', [
                'locale' => $locale,
                'subjectLine' => $subject,
                'body' => $body,
                'receivingRecordNo' => $this->testRequest->receiving_record_no,
                'actorName' => $this->actorName,
                'labelReceiving' => __('notifications.email_receiving_record', [], $locale),
                'labelBy' => __('notifications.email_by', [], $locale),
                'footer' => __('notifications.email_footer', [], $locale),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'test_request_id' => $this->testRequest->id,
            'receiving_record_no' => $this->testRequest->receiving_record_no,
            'qoyod_customer_id' => $this->testRequest->qoyod_customer_id,
            'actor_name' => $this->actorName,
            'batch_id' => $this->meta['batch_id'] ?? null,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function translationParams(): array
    {
        return [
            'record' => $this->testRequest->receiving_record_no,
            'batch' => (string) ($this->meta['batch_id'] ?? ''),
        ];
    }
}
