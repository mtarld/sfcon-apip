<?php

declare(strict_types=1);

namespace App\Api\Dto;

use App\Entity\Book as BookEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: BookEntity::class)]
final class UpdateBook
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Map(target: 'title')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $description;
}
