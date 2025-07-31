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
        $pocetnaCena = $this->faker->numberBetween(10,100000);
        return [
            'pocetnaCena'=>$pocetnaCena,
            'trenutnaCena' => $this->faker->boolean(20)? 0 
                : $this->faker->numberBetween($pocetnaCena, 1000000),
            'datumPocetka' => $this->faker->dateTimeBetween('now', '+1 week'),
            'statusAukcije' => $this->faker->randomElement(['aktivna', 'zavrsena', 'predstojeca']),
        ];
    }

    public function predstojeca(): static
    {
        return $this->state(function (array $attributes) {
            $pocetnaCena = $attributes['pocetnaCena'] ?? $this->faker->numberBetween(10, 100000);

            return [
                'statusAukcije' => 'predstojeca',
                'trenutnaCena' => $pocetnaCena,
            ];
        });
    }
}
