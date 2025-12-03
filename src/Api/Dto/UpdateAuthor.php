<?php

declare(strict_types=1);

namespace App\Api\Dto;

use App\Entity\Author as AuthorEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: AuthorEntity::class)]
final class UpdateAuthor
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $fullname;
}
