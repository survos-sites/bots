<?php

namespace App\Dto;

use NeuronAI\StructuredOutput\SchemaProperty;

class UrlAction
{
    #[SchemaProperty(description: 'The url to analyze', required: true)]
    public string $url;

    #[SchemaProperty(description: 'the action that the user would like to do, like analyze, summarize.', required: false)]
    public string $action;
}
