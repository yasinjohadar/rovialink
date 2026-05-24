<?php

namespace App\DataTransferObjects;

class PaymentInitiationResult
{
    public function __construct(
        public readonly ?string $redirectUrl = null,
        public readonly ?string $view = null,
        public readonly array $viewData = [],
        public readonly array $metadata = [],
    ) {}

    public function requiresRedirect(): bool
    {
        return $this->redirectUrl !== null;
    }

    public function requiresView(): bool
    {
        return $this->view !== null;
    }
}
