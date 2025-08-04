<?php

namespace Database\Factories;

use App\Models\Korisnik;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Korisnik>
 */
class KorisnikFactory extends Factory
{
    protected $model = Korisnik::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ime' => $this->faker->firstName(),
            'prezime' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'lozinka' => Hash::make('password'),
            'broj_telefona' => $this->faker->phoneNumber(),
            'adresa' => $this->faker->address(),
            'stanje_na_racunu' => $this->faker->numberBetween(0,1000000),
            
            
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

}
