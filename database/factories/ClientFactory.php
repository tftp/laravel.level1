<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Propaganistas\LaravelPhone\PhoneNumber;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'last_name' => $this->faker->lastName(),
            'phone' => str_replace('+', '', PhoneNumber::make('9' . $this->faker->randomNumber(3, true) . $this->faker->randomNumber(3, true) . $this->faker->randomNumber(3, true), 'RU')),
            'service_id' => 1,
        ];
    }
}
