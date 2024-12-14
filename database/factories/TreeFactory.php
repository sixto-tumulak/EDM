<?php

namespace Database\Factories;

use App\Models\Tree;
use Illuminate\Database\Eloquent\Factories\Factory;

class TreeFactory extends Factory
{
    protected $model = Tree::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Generates a random word as the tree name
        ];
    }
}