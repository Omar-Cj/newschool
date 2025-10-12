<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent when export fails
 */
class ExportFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $reportId;
    protected string $format;
    protected string $errorMessage;

    /**
     * Create notification instance
     *
     * @param int $reportId Report identifier
     * @param string $format Export format
     * @param string $errorMessage Error message
     */
    public function __construct(int $reportId, string $format, string $errorMessage)
    {
        $this->reportId = $reportId;
        $this->format = $format;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get notification delivery channels
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get mail representation
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Report Export Failed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your report export could not be completed.')
            ->line('Report ID: ' . $this->reportId)
            ->line('Format: ' . strtoupper($this->format))
            ->line('Error: ' . $this->errorMessage)
            ->line('Please try again or contact support if the issue persists.')
            ->action('View Reports', url('/reports'))
            ->line('We apologize for the inconvenience.');
    }

    /**
     * Get database representation
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Report Export Failed',
            'message' => "Export failed for report #{$this->reportId}",
            'report_id' => $this->reportId,
            'format' => $this->format,
            'error' => $this->errorMessage,
            'failed_at' => now()->toDateTimeString(),
        ];
    }
}
