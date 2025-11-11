<?php

declare(strict_types=1);

namespace App\ApiResource;

use App\Entity\Book as BookEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(target: BookEntity::class)]
final class CreateBook
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Map(target: 'title')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $description;

    #[Assert\NotBlank]
    #[Assert\Isbn]
    public string $isbn;
}
