<?php

declare(strict_types=1);

namespace App\Api\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class DiscountBook
{
    #[Assert\Range(min: 0, max: 100)]
    public int $percentage;
}
