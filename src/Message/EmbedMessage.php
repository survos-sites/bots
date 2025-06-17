<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class EmbedMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

     public function __construct(
     public readonly string $text,
         public readonly string $agentClass
     ) {
     }
}
