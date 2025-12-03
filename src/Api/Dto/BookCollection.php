<?php

declare(strict_types=1);

namespace App\Api\Dto;

use App\Api\Resource\Author as AuthorResource;
use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: BookEntity::class)]
final class BookCollection
{
    public int $id;

    #[Map(source: 'title')]
    public string $name;

    public string $isbn;

    public AuthorResource $author;
}
