<?php

namespace App\Console\Commands\Energy;

use App\Mail\EnergyPriceAlertMail;
use App\Services\EnergyPriceApi\Getter\EnergyPriceApiGetPrice;
use App\Traits\Logger\Logger;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

// php artisan command:EnergyPriceAlert
class EnergyPriceAlertCommand extends Command
{
    protected $signature = 'command:EnergyPriceAlert';

    private string $channel;

    public function handle()
    {

        $this->channel = 'energy_prices';

        Logger::deleteChannel($this->channel);

        $fromDate = Carbon::tomorrow()->startOfDay()->toIso8601String();
        $tillDate = Carbon::tomorrow()->endOfDay()->toIso8601String();

        $params = [
            'fromDate' => $fromDate,
            'tillDate' => $tillDate,
            'interval' => 4,
            'usageType' => 1, // 1 = electra, 2 = gas
            'inclBtw' => true,
        ];

        $energyPriceApiGetPrice = new EnergyPriceApiGetPrice;
        $results['electra'] = $energyPriceApiGetPrice->get($params);

        $params = [
            'fromDate' => $fromDate,
            'tillDate' => $tillDate,
            'interval' => 4,
            'usageType' => 2, // 1 = electra, 2 = gas
            'inclBtw' => true,
        ];

        $energyPriceApiGetPrice = new EnergyPriceApiGetPrice;
        $results['gas'] = $energyPriceApiGetPrice->get($params);

        if ($results) {
            // Mail::to('pie@micturion.com')->send(new EnergyPriceAlertMail($results));
        }

        // Logger::echo($this->channel);
    }
}
