<?php

namespace App\Helpers;

use DateInterval;
use DateTime;

class DateTimeHelper
{
    public static function convertMs(string $duration): string
    {
        preg_match('/(?:(\d+)m)?(?:(\d+)s)?/', $duration, $matches);

        $minutes = isset($matches[1]) ? (int) $matches[1] : 0;
        $seconds = isset($matches[2]) ? (int) $matches[2] : 0;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public static function msToTime(?int $ms): string
    {
        if (!$ms || $ms < 0) {
            return '00:00:00';
        }

        $dt = new DateTime('@0'); // Epoch time
        $dt->add(new DateInterval('PT' . floor($ms / 1000) . 'S'));

        return $dt->format('i:s');
    }
}
