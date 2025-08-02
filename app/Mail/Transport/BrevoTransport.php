<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BrevoTransport extends AbstractTransport
{
    protected HttpClientInterface $client;
    protected string $apiKey;

    public function __construct(HttpClientInterface $client, string $apiKey)
    {
        parent::__construct();
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            throw new TransportException('Only Email instances are supported.');
        }

        $from = $email->getFrom()[0] ?? null;
        $to = $email->getTo();

        if (!$from || empty($to)) {
            throw new TransportException('Missing sender or recipient.');
        }

        $payload = [
            'sender' => [
                'name' => $from->getName(),
                'email' => $from->getAddress(),
            ],
            'to' => array_map(function ($recipient) {
                return [
                    'email' => $recipient->getAddress(),
                    'name' => $recipient->getName() ?? '', // Prevent missing name
                ];
            }, $to),
            'subject' => $email->getSubject(),
        ];

        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $email->getHtmlBody();
        }

        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        try {
            $response = $this->client->request('POST', 'https://api.brevo.com/v3/smtp/email', [
                'headers' => [
                    'api-key' => $this->apiKey,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $data = $response->toArray(false);

            if ($statusCode >= 200 && $statusCode < 300) {
                // Optional: Log messageId or attach it to SentMessage metadata
                // $message->getHeaders()->addTextHeader('X-Brevo-Message-ID', $data['messageId'] ?? 'N/A');
                return;
            }

            throw new TransportException('Brevo API error: ' . json_encode($data));
        } catch (\Throwable $e) {
            throw new TransportException('Brevo send failed: ' . $e->getMessage(), 0, $e);
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
