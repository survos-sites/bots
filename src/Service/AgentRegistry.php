<?php

// src/Service/AgentRegistry.php
namespace App\Service;

use App\Agent\AppAgentInterface;
use App\Agent\AppRAGAgent;
use App\Agent\MeiliRAG;
use App\Agent\RappAgent;
use Inspector\Inspector;
use NeuronAI\Observability\AgentMonitoring;
use NeuronAI\RAG\RAG;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use NeuronAI\AgentInterface;
use Symfony\Contracts\Service\ServiceCollectionInterface;

class AgentRegistry
{
    public function __construct(
        private Inspector $inspector,
//        private RappAgent $rappAgent,
//        #[AutowireLocator('app.agent', indexAttribute: 'index')]
//        private ContainerInterface $agents,

        #[AutowireLocator(AppAgentInterface::class, indexAttribute: 'key')]
        private ServiceCollectionInterface $agents,
    )
    {
//        dd($this->agents);
//        foreach ($this->agents->getProvidedServices() as $key => $agent) {
//            dd($key, $agent);
//        }
//        return array_keys($this->agents->getProvidedServices());
//        dd($this->agents);
    }

    public function agents(): iterable
    {
        return array_keys($this->agents->getProvidedServices());
    }

    public function get(string $code): AgentInterface|AppAgentInterface|AppRAGAgent
    {
        $agent = $this->agents->get($code);
        $agent->observe(new AgentMonitoring($this->inspector));
        return $agent;
    }
}
