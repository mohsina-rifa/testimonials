<?php

namespace App\Exceptions;

use RuntimeException;

class PlanLimitReachedException extends RuntimeException
{
    public static function forSpaces(): self
    {
        return new self('You have reached the maximum number of spaces for your plan.');
    }

    public static function forTestimonials(): self
    {
        return new self('This space is not accepting new testimonials right now.');
    }
}
