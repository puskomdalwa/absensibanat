<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->username,
            'role_id' => 2,
            'departemen_id' => 2,
            'jenis_kelamin' => $this->faker->randomElement(\Helper::getEnumValues('users', 'jenis_kelamin', ['*'])),
            'email' => $this->faker->unique()->safeEmail,
            'password' => \Hash::make('dalwa123'),
        ];
    }
}
