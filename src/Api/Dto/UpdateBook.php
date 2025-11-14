<?php

declare(strict_types=1);

namespace App\Api\Dto;

use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: BookEntity::class)]
final class UpdateBook
{
    #[Map(target: 'title')]
    public string $name;

    public string $description;
}
