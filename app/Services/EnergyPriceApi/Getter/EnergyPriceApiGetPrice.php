<?php

namespace App\Services\EnergyPriceApi\Getter;

use App\Traits\Logger\Logger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class EnergyPriceApiGetPrice
{
    private string $channel;

    private $results;

    public function __construct() {}

    public function get(array $params)
    {

        $this->channel = 'energy_prices';

        // Base URL of the API
        $baseUrl = 'https://api.energyzero.nl/v1/energyprices';

        $date = date('d M Y', Carbon::tomorrow()->timestamp);

        // Make the GET request
        $url = url()->query($baseUrl, $params);
        // $response = Http::get($baseUrl, $params);

        $total = 0;
        $average = 0;
        // $skip = ['00:00', '01:00', '02:00', '03:00'];
        $skip = [];

        // Check if the request was successful
        if (// $response->successful()) {
            // Get the JSON data as an array or object
            $data = // $response->json();
            foreach ($data['Prices'] as $price) {

                $timeZone = env('APP_TIMEZONE', 'UTC'); // Default to 'UTC' if not set

                $niceTime = Carbon::parse($price['readingDate'])->setTimezone($timeZone)->timestamp;

                $time = date('H:i', $niceTime); // Outputs the datetime in the desired timezone

                if (!in_array($time, $skip)) {

                    // Add 59 minutes to the initial time
                    $endTime = date('H:i', strtotime('+59 minutes', $niceTime));

                    $total = $total + $price['price'];

                    $results['prices'][] = [
                        'price' => $price['price'],
                        'start_time' => $time,
                        'end_time' => $endTime,
                    ];
                }
            }

            $results['date'] = $date;
            $results['average'] = $total / 24;
            $results['total'] = $total;

            if (!$results) {
                Logger::log('error', $this->channel, 'No results ' . $url);
            } else {
                Logger::log('info', $this->channel, 'Prices found');
            }
        } else {
            Logger::log('error', $this->channel, 'No response ' . $url);
        }

        return $results;
    }
}
