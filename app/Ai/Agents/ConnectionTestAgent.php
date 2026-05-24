<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

class ConnectionTestAgent implements Agent, Conversational
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'You are a connection test assistant. Reply with exactly: OK';
    }

    public function messages(): iterable
    {
        return [];
    }
}
