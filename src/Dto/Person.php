<?php
namespace App\Dto;

use NeuronAI\StructuredOutput\SchemaProperty;

class Person
{
    #[SchemaProperty(description: 'The user name.', required: true)]
    public string $name;

    #[SchemaProperty(description: 'What the user love to eat.', required: false)]
    public string $preference;
}
