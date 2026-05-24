<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class ChatReplyAgent implements Agent, Conversational
{
    use Promptable;

    public function __construct(
        protected string $systemInstructions = '',
    ) {}

    public function instructions(): Stringable|string
    {
        return $this->systemInstructions ?: 'أنت مساعد ذكي مفيد. أجب باختصار ووضوح.';
    }

    public function messages(): iterable
    {
        return [];
    }
}
