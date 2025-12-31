<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnergyPriceAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $date;

    public $average;

    public $results;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function build()
    {

        return $this->view('emails.energy-price-alert-mail')
            ->from('pie@micturion.com', 'Micturion')
            ->subject('ANWB Tarieven voor ' . $this->results['electra']['date'])
            ->with([
                'results' => $this->results,
            ]);
    }
}
