<?php

namespace App\Contracts\Payments;

use App\Data\Payments\PaymentInitResult;
use App\Data\Payments\PaymentVerifyResult;
use App\Models\Order;
use App\Models\PaymentTransaction;

interface PaymentGatewayInterface
{
    public function gateway(): string;

    public function isConfigured(): bool;

    public function initiate(Order $order, PaymentTransaction $transaction): PaymentInitResult;

    public function verify(PaymentTransaction $transaction, array $callbackData = []): PaymentVerifyResult;
}
