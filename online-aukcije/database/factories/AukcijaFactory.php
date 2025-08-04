<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Aukcija;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aukcija>
 */
class AukcijaFactory extends Factory
{
    protected $model = Aukcija::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pocetna_cena = $this->faker->numberBetween(10,100000);
        return [
            'pocetna_cena'=>$pocetna_cena,
            'trenutna_cena' => $this->faker->boolean(20)? 0 
                : $this->faker->numberBetween($pocetna_cena, 1000000),
            'datum_pocetka' => $this->faker->dateTimeBetween('now', '+1 week'),
            'status_aukcije' => $this->faker->randomElement(['aktivna', 'zavrsena', 'predstojeca']),
        ];
    }

    public function predstojeca(): static
    {
        return $this->state(function (array $attributes) {
            $pocetna_cena = $attributes['pocetna_cena'] ?? $this->faker->numberBetween(10, 100000);

            return [
                'status_aukcije' => 'predstojeca',
                'trenutna_cena' => $pocetna_cena,
            ];
        });
    }
}
