<?php

namespace App\Traits;

use NeuronAI\AgentInterface;
use NeuronAI\SystemPrompt;

trait IdentityTrait
{
    public function getSystemPrompt(): SystemPrompt
    {
        return new SystemPrompt(["You are a generic agent"]);
    }

    public function getIdentity(): array
    {
        $identity = [];
        foreach ($this->getSystemPrompt()->background as $string) {
            $string  =  str_replace("You are", "I am", $string);
            $string = preg_replace('/^You /', 'I ', $string);
            $identity[] = $string;
        }
        return $identity;

    }


}
