<?php

declare(strict_types=1);

namespace App\Api\Dto;

use App\Entity\Author as AuthorEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: AuthorEntity::class)]
final class AuthorCollection
{
    public int $id;
    public string $fullname;
}
