<?php

namespace Database\Factories;

use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsersFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Users::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'api_key' => sha1($this->faker->firstName()) . sha1($this->faker->lastName())
        ];
    }
}
