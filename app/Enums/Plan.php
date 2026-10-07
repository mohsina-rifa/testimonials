<?php

namespace App\Enums;

enum Plan: string
{
    case Free = 'free';
    case Pro = 'pro';

    public function maxSpaces(): int
    {
        return config("plans.{$this->value}.max_spaces");
    }

    public function maxTestimonialsPerSpace(): int
    {
        return config("plans.{$this->value}.max_testimonials_per_space");
    }
}
