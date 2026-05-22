<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailSender
{
    public function __construct(
        protected EmailTemplateService $templates
    ) {
    }

    /**
     * Send a templated email for a given event.
     *
     * @param  string  $event
     * @param  \App\Models\User|string  $recipient
     * @param  array<string, mixed>  $data
     * @param  string|null  $locale
     */
    public function send(string $event, User|string $recipient, array $data = [], ?string $locale = null): void
    {
        $template = $this->templates->getTemplate($event, $locale);
        if (!$template) {
            return;
        }

        $email = $recipient instanceof User ? $recipient->email : $recipient;
        if (!$email) {
            return;
        }

        $rendered = $this->templates->render($template, $data);

        Mail::html($rendered['html'], function ($message) use ($email, $rendered, $data) {
            $message->to($email)
                ->subject($rendered['subject']);

            if (!empty($data['from_email'])) {
                $message->from($data['from_email'], $data['from_name'] ?? null);
            }
        });
    }
}

