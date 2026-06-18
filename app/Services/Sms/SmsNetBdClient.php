<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNetBdClient
{
    public const SEND_URL = 'https://api.sms.net.bd/sendsms';

    public const BALANCE_URL = 'https://api.sms.net.bd/user/balance/';

    public const REPORT_URL = 'https://api.sms.net.bd/report/request/';

    public function send(
        string $apiKey,
        string $phone,
        string $message,
        ?string $senderId = null,
        ?string $schedule = null,
    ): SmsSendResult {
        if ($apiKey === '') {
            return new SmsSendResult(false, 405, 'SMS API key is not configured.');
        }

        $to = $this->normalizeRecipients($phone);
        if ($to === '') {
            return new SmsSendResult(false, 416, 'No valid Bangladesh phone number found.');
        }

        $msg = trim($message);
        if ($msg === '') {
            return new SmsSendResult(false, 414, 'Message cannot be empty.');
        }

        $payload = [
            'api_key' => $apiKey,
            'msg' => $msg,
            'to' => $to,
        ];

        if ($senderId) {
            $payload['sender_id'] = $senderId;
        }

        if ($schedule) {
            $payload['schedule'] = $schedule;
        }

        return $this->request('POST', self::SEND_URL, $payload, expectRequestId: true);
    }

    public function balance(string $apiKey): SmsSendResult
    {
        if ($apiKey === '') {
            return new SmsSendResult(false, 405, 'SMS API key is not configured.');
        }

        $result = $this->request('GET', self::BALANCE_URL, ['api_key' => $apiKey]);

        if (! $result->success) {
            return $result;
        }

        return new SmsSendResult(
            true,
            0,
            $result->message,
            balance: $result->balance,
        );
    }

    public function report(string $apiKey, int $requestId): SmsSendResult
    {
        if ($apiKey === '') {
            return new SmsSendResult(false, 405, 'SMS API key is not configured.');
        }

        $url = rtrim(self::REPORT_URL, '/').'/'.$requestId.'/';

        return $this->request('GET', $url, ['api_key' => $apiKey]);
    }

    public static function errorMessage(int $code, ?string $apiMessage = null): string
    {
        $mapped = match ($code) {
            0 => 'Success',
            400 => 'The request was rejected due to a missing or invalid parameter.',
            403 => 'You do not have permission to perform this request.',
            404 => 'The requested resource was not found.',
            405 => 'Authorization required. Check your API key.',
            409 => 'Unknown error occurred on the SMS server.',
            410 => 'SMS account expired.',
            411 => 'Reseller account expired or suspended.',
            412 => 'Invalid schedule date or time.',
            413 => 'Invalid Sender ID.',
            414 => 'Message is empty.',
            415 => 'Message is too long.',
            416 => 'No valid phone number found.',
            417 => 'Insufficient SMS balance.',
            420 => 'Message content blocked.',
            421 => 'You can only send SMS to your registered phone until the first balance recharge.',
            default => $apiMessage ?: "SMS provider error (code {$code}).",
        };

        if ($apiMessage && $code !== 0 && $apiMessage !== 'Success') {
            return $mapped.' '.$apiMessage;
        }

        return $mapped;
    }

    private function request(
        string $method,
        string $url,
        array $params,
        bool $expectRequestId = false,
    ): SmsSendResult {
        try {
            $pending = Http::timeout(20)->acceptJson();

            $response = $method === 'GET'
                ? $pending->get($url, $params)
                : $pending->asForm()->post($url, $params);

            if ($response->status() === 403) {
                return new SmsSendResult(false, 403, self::errorMessage(403));
            }

            if ($response->status() === 404) {
                return new SmsSendResult(false, 404, self::errorMessage(404));
            }

            if ($response->status() === 405) {
                return new SmsSendResult(false, 405, self::errorMessage(405));
            }

            $body = $response->json();
            if (! is_array($body)) {
                Log::warning('SMS.net.bd invalid response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return new SmsSendResult(
                    false,
                    409,
                    'Invalid response from SMS provider.',
                );
            }

            $errorCode = (int) ($body['error'] ?? 409);
            $apiMessage = (string) ($body['msg'] ?? '');
            $message = self::errorMessage($errorCode, $apiMessage);

            if ($errorCode !== 0) {
                Log::warning('SMS.net.bd API error', [
                    'error' => $errorCode,
                    'msg' => $apiMessage,
                    'status' => $response->status(),
                ]);

                return new SmsSendResult(false, $errorCode, $message);
            }

            $data = is_array($body['data'] ?? null) ? $body['data'] : [];
            $requestId = isset($data['request_id']) ? (int) $data['request_id'] : null;
            $balance = isset($data['balance']) ? (float) $data['balance'] : null;

            if ($expectRequestId && $requestId === null) {
                return new SmsSendResult(true, 0, $apiMessage ?: 'SMS submitted.');
            }

            return new SmsSendResult(
                true,
                0,
                $apiMessage ?: 'Success',
                requestId: $requestId,
                balance: $balance,
            );
        } catch (\Throwable $e) {
            Log::error('SMS.net.bd request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return new SmsSendResult(false, 409, 'Could not reach SMS provider: '.$e->getMessage());
        }
    }

    public function normalizeRecipients(string $phones): string
    {
        $numbers = preg_split('/[\s,;]+/', trim($phones)) ?: [];
        $normalized = [];

        foreach ($numbers as $number) {
            $formatted = $this->normalizePhone($number);
            if ($formatted !== '') {
                $normalized[] = $formatted;
            }
        }

        return implode(',', array_values(array_unique($normalized)));
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '880') && strlen($digits) === 13) {
            return $digits;
        }

        if (str_starts_with($digits, '01') && strlen($digits) === 11) {
            return '88'.$digits;
        }

        if (str_starts_with($digits, '1') && strlen($digits) === 10) {
            return '880'.$digits;
        }

        return '';
    }
}
