<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],
    Symfony\UX\Turbo\TurboBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    Survos\CodeBundle\SurvosCodeBundle::class => ['dev' => true, 'test' => true],
    Inspector\Symfony\Bundle\InspectorBundle::class => ['all' => true],
    Survos\DeploymentBundle\SurvosDeploymentBundle::class => ['dev' => true],
    Survos\PicoBundle\SurvosPicoBundle::class => ['all' => true],
    Survos\CommandBundle\SurvosCommandBundle::class => ['all' => true],
    Jwage\PhpAmqpLibMessengerBundle\PhpAmqpLibMessengerBundle::class => ['all' => true],
    Survos\JsonlBundle\SurvosJsonlBundle::class => ['all' => true],
    Survos\CoreBundle\SurvosCoreBundle::class => ['all' => true],
    Survos\MeiliBundle\SurvosMeiliBundle::class => ['all' => true],
    Symfony\AI\AiBundle\AiBundle::class => ['all' => true],
//    Symfony\AI\McpBundle\McpBundle::class => ['all' => true],

];
