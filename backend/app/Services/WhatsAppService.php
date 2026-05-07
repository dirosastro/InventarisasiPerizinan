<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url', 'http://localhost:3000');
    }

    /**
     * Mengirim pesan WhatsApp melalui bot API.
     *
     * @param string $number Nomor HP (format: 628xxx atau 08xxx)
     * @param string $message Isi pesan
     * @return array|bool
     */
    public function sendMessage($number, $message)
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/send-message", [
                'number' => $number,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('WhatsApp API Error: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception: ' . $e->getMessage());
            return false;
        }
    }
}
