<?php

namespace App\Services;

use App\Models\Application;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationService
{
    /**
     * Render template (by key) dan kirim email ke pelamar, sekaligus catat hasilnya
     * ke notification_logs (pending -> sent/failed).
     *
     * Placeholder di subject/body template pakai format {{key}}, contoh: {{applicant_name}}.
     */
    public function send(Application $application, string $templateKey, array $placeholders = [], string $channel = 'email'): void
    {
        $template = NotificationTemplate::where('key', $templateKey)->first();

        if (! $template) {
            Log::warning("Notifikasi dilewati: template '{$templateKey}' belum ada di notification_templates. Jalankan NotificationTemplateSeeder.");

            return;
        }

        $applicantEmail = $application->applicant?->email;

        if (empty($applicantEmail)) {
            Log::warning("Notifikasi dilewati: applicant untuk application #{$application->id} tidak punya email.");

            return;
        }

        $log = NotificationLog::create([
            'application_id' => $application->id,
            'notification_template_id' => $template->id,
            'channel' => $channel,
            'status' => 'pending',
        ]);

        $subject = $this->renderPlaceholders($template->subject, $placeholders);
        $body = $this->renderPlaceholders($template->body, $placeholders);

        try {
            Mail::html(nl2br(e($body)), function ($message) use ($applicantEmail, $subject) {
                $message->to($applicantEmail)->subject($subject);
            });

            $log->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Gagal kirim notifikasi '{$templateKey}' ke {$applicantEmail}: {$e->getMessage()}");
        }
    }

    private function renderPlaceholders(string $text, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', (string) $value, $text);
        }

        return $text;
    }
}