<?php

namespace App\Entity;

use App\Repository\VectorStoreRepository;
use Doctrine\ORM\Mapping as ORM;
use NeuronAI\RAG\VectorStore\Doctrine\DoctrineEmbeddingEntityBase;
use NeuronAI\RAG\VectorStore\Doctrine\VectorType;

use NeuronAI\RAG\VectorStore\TypesenseVectorStore;

#[ORM\Entity(repositoryClass: VectorStoreRepository::class)]
class VectorStore# extends DoctrineEmbeddingEntityBase
{
//    #[ORM\Column(type: VectorType::VECTOR, length: 1024)]
    public array $embedding = [];


}
