<?php

namespace Database\Factories;

use App\Models\Ponuda;
use App\Models\Aukcija;
use App\Models\Korisnik;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ponuda>
 */
class PonudaFactory extends Factory
{
    protected $model = Ponuda::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $aukcija = Aukcija::factory()->create();
        $startDate = $aukcija->datum_pocetka ? Carbon::parse($aukcija->datum_pocetka) : Carbon::now();
        $endDate = $startDate->copy()->addMinutes($this->faker->numberBetween(15, 60));

        return [
            'iznos' => $this->faker->numberBetween(10, 500000),
            'vreme_ponude' => $this->faker->dateTimeBetween($startDate, $endDate),
            'aukcija_id' => $aukcija->id,
            'korisnik_id' => Korisnik::factory(),
        ];
    }

    /**
     * Omogućava postavljanje ponude za specifičnu, već postojeću aukciju.
     */
    public function forAukcija(Aukcija $aukcija): static
    {
        return $this->state(function (array $attributes) use ($aukcija) {
            $startDate = Carbon::parse($aukcija->datum_pocetka);
            $endDate = $startDate->copy()->addMinutes($this->faker->numberBetween(15, 60));

            return [
                'aukcija_id' => $aukcija->id,
                'vreme_ponude' => $this->faker->dateTimeBetween($startDate, $endDate),
                // Iznos ponude je veći od trenutne cene aukcije
                'iznos' => $this->faker->numberBetween($aukcija->trenutna_cena + 1, $aukcija->trenutna_cena + 1000),
            ];
        });
    }

}
