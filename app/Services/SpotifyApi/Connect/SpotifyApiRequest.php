<?php

namespace App\Services\SpotifyApi\Connect;

use SpotifyWebAPI\Request;

class SpotifyApiRequest extends Request
{
    private int $delayMs;

    public function __construct(int $delayMs = 0, array|object $options = [])
    {
        $this->delayMs = $delayMs;
        parent::__construct($options);
    }

    public function send(string $method, string $url, string|array|object $parameters = [], array $headers = []): array
    {
        if ($this->delayMs > 0) {
            usleep($this->delayMs * 1000);
        }

        return parent::send($method, $url, $parameters, $headers);
    }
}
