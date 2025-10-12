<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent when export is ready for download
 */
class ExportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $filename;
    protected string $downloadUrl;
    protected string $format;

    /**
     * Create notification instance
     *
     * @param string $filename Generated filename
     * @param string $downloadUrl Download URL
     * @param string $format Export format
     */
    public function __construct(string $filename, string $downloadUrl, string $format)
    {
        $this->filename = $filename;
        $this->downloadUrl = $downloadUrl;
        $this->format = $format;
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
            ->subject('Report Export Ready')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your report export is ready for download.')
            ->line('Format: ' . strtoupper($this->format))
            ->line('Filename: ' . $this->filename)
            ->action('Download Report', $this->downloadUrl)
            ->line('This download link will expire in 24 hours.')
            ->line('Thank you for using our system!');
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
            'title' => 'Report Export Ready',
            'message' => "Your report export ({$this->filename}) is ready for download.",
            'download_url' => $this->downloadUrl,
            'filename' => $this->filename,
            'format' => $this->format,
            'expires_at' => now()->addHours(24)->toDateTimeString(),
        ];
    }
}
