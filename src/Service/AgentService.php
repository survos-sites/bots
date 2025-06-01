<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

class AgentService
{
    public function __construct(
        #[AutowireIterator('neuron.agent')] private iterable $agents,
    )
    {
    }

    public function getAgents(): iterable
    {
        return $this->agents;
    }

    public function agentsByCode(): array
    {
        $agentsByCode = [];
        foreach ($this->agents as $agent) {
            $shortName = new \ReflectionClass($agent)->getShortName();
            $shortName = preg_replace('/Agent$/', '', $shortName);
            $agentsByCode[$shortName] = $agent;
        }
        return $agentsByCode;

    }

}
