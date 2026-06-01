<?php

namespace App\Console\Commands;

use App\Mail\TestMailgunMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMailCommand extends Command
{
    protected $signature = 'mail:test {email? : Recipient email address}';

    protected $description = 'Send a test email via the configured mailer (Mailgun)';

    public function handle(): int
    {
        $email = $this->argument('email') ?? config('mail.from.address');

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');

            return self::FAILURE;
        }

        $mailer = config('mail.default');
        $this->info("Mailer: {$mailer}");
        $this->info("Domain: ".config('services.mailgun.domain'));
        $this->info("Sending test email to: {$email}");

        try {
            Mail::to($email)->send(new TestMailgunMail(now()->toDateTimeString()));

            $this->newLine();
            $this->info('Test email sent successfully.');

            if (str_contains((string) config('services.mailgun.domain'), 'sandbox')) {
                $this->warn('Sandbox domain: recipient must be authorized in Mailgun (Authorized Recipients).');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to send: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
