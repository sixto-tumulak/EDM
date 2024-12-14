<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        return [
            'title' => $this->faker->jobTitle(),
            'user_id' => $this->faker->numberBetween(1),
            'tree' => $this->faker->numberBetween(1, 100),
            'quantity' => $this->faker->numberBetween(1, 10),
            'job_description' => $this->faker->text(100),
        ];
    }
}