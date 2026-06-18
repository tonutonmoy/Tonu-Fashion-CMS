<?php

namespace App\Services\Sms;

readonly class SmsSendResult
{
    public function __construct(
        public bool $success,
        public int $errorCode,
        public string $message,
        public ?int $requestId = null,
        public ?float $balance = null,
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->errorCode,
            'message' => $this->message,
            'request_id' => $this->requestId,
            'balance' => $this->balance,
        ];
    }
}
