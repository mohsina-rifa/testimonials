<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $alice = User::factory()->create(['name' => 'Alice', 'email' => 'alice@acme.com']);
        $bob = User::factory()->create(['name' => 'Bob', 'email' => 'bob@studio.io']);

        $acmeProduct = $this->space($alice, 'Acme Product', 'acme-product');
        $acmeConsulting = $this->space($alice, 'Acme Consulting', 'acme-consulting');
        $course = $this->space($bob, "Bob's Course", 'bobs-course');
        $this->space($bob, "Bob's Newsletter", 'bobs-newsletter');

        $this->testimonial($acmeProduct, 'Dana', 'dana@gmail.com', 'Globex', 5, 'Love it!');
        $this->testimonial($acmeProduct, 'Eli', 'eli@initech.com', 'Initech', 4, 'Saved us hours');
        $this->testimonial($acmeConsulting, 'Dana', 'dana@gmail.com', 'Globex', 5, 'Great consulting');
        $this->testimonial($course, 'Dana', 'dana@gmail.com', null, null, 'Best course');
        $this->testimonial($course, 'Fay', 'fay@hooli.com', 'Hooli', 3, 'Pretty good');
    }

    private function space(User $owner, string $title, string $slug): Space
    {
        return Space::factory()->for($owner)->create(['title' => $title, 'slug' => $slug]);
    }

    private function testimonial(Space $space, string $name, string $email, ?string $company, ?int $rating, string $text): Testimonial
    {
        return Testimonial::factory()->for($space)->consented()->create([
            'submitter_name' => $name,
            'submitter_email' => $email,
            'company_name' => $company,
            'rating' => $rating,
            'testimonial_text' => $text,
        ]);
    }
}
