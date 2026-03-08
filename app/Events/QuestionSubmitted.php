<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionSubmitted
{
    use Dispatchable, SerializesModels;

    public $question;

    public function __construct($question)
    {
        $this->question = $question;
    }
}
