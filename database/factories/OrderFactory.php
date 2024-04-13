<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status=fake()->randomElement(['Pending','Accepted','Denied','Done']);
        return [
            'patient_name' => fake()->name(),
            'center_name' => fake()->company(),
            'veneer' => fake()->text(15),
            'crown' => fake()->text(15),
            'inlay_onlay' => fake()->text(15),
            'ceramicBuildUp' => fake()->text(15),
            'ceramicFacing' => fake()->text(15),
            'fullAnatomic' => fake()->text(15),
            'fullMetal' => fake()->text(15),
            'PFM' => fake()->text(15),
            'DSD' => fake()->text(15),
            'mockUp' => fake()->text(15),
            'printedModel' => fake()->text(15),
            'PMMA' => fake()->text(15),
            'upper_color' => fake()->colorName,
            'middle_color' => fake()->colorName,
            'lower_color' => fake()->colorName,
            'teethCount' => fake()->text(15),
            'connected' => fake()->text(15),
            'separate' => fake()->text(15),
            'notes' => fake()->optional(0.1)->text,
            'submitted_at'=>now(),
            'status'=> $status,
            'stage'=> $status!='Accepted'?null:(fake()->randomElement(['shaping','making figure','doing something','last touches'])),
        ];

    }
}
