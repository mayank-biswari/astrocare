<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PoojaBooked
{
    use Dispatchable, SerializesModels;

    public $pooja;

    public function __construct($pooja)
    {
        $this->pooja = $pooja;
    }
}
