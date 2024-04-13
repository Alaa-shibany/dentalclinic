<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GalleryPiece>
 */
class GalleryPieceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_uri'=>fake()->imageUrl(),
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'favorite' => fake()->boolean()
        ];
    }
}
