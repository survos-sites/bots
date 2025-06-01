<?php

namespace App\Entity;

use App\Repository\VectorStoreRepository;
use Doctrine\ORM\Mapping as ORM;
use NeuronAI\RAG\VectorStore\Doctrine\DoctrineEmbeddingEntityBase;

#[ORM\Entity(repositoryClass: VectorStoreRepository::class)]
class VectorStore extends DoctrineEmbeddingEntityBase
{
}
