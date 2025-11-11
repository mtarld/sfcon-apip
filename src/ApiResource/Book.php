<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
    stateOptions: new Options(entityClass: BookEntity::class),
    operations: [
        new Get(uriTemplate: '/books/{id}', uriVariables: ['id']),
        new Post(uriTemplate: '/books', input: CreateBook::class),
    ],
)]
#[Map(target: BookEntity::class)]
final class Book
{
    public int $id;

    #[Map(target: 'title')]
    public string $name;

    public string $description;

    public string $isbn;
}
