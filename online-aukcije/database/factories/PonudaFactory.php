<?php

namespace Database\Factories;

use App\Models\Ponuda;
use App\Models\Aukcija; // Moramo da uvezemo Aukcija jer je koristimo u callbacku
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
        $aukcija = Aukcija::factory()->create(); // Kreira aukciju za ovu ponudu
        $startDate = Carbon::parse($aukcija->datumPocetka);
        // Simulira trajanje aukcije od 15 do 60 minuta od početka
        $endDate = $startDate->copy()->addMinutes($this->faker->numberBetween(15, 60));

        return [
            // Ispravljena metoda za 'iznos'
            'iznos' => $this->faker->numberBetween(10, 500000), // Primer: iznos između 10 i 500.000
            'vremePonude' => $this->faker->dateTimeBetween($startDate, $endDate),
            'aukcijaID' => $aukcija->id,
            'korisnikID' => Korisnik::factory(), // Kreira korisnika za ponudu
        ];
    }

    /**
     * Omogućava postavljanje ponude za specifičnu, već postojeću aukciju.
     */
    public function forAukcija(Aukcija $aukcija): static
    {
        return $this->state(function (array $attributes) use ($aukcija) {
            $startDate = Carbon::parse($aukcija->datumPocetka);
            // Simulira trajanje za ovu konkretnu aukciju
            $endDate = $startDate->copy()->addMinutes($this->faker->numberBetween(15, 60));

            return [
                'aukcijaID' => $aukcija->id,
                'vremePonude' => $this->faker->dateTimeBetween($startDate, $endDate),
                // Iznos ponude je veći od trenutne cene aukcije
                'iznos' => $this->faker->numberBetween($aukcija->trenutnaCena + 1, $aukcija->trenutnaCena + 1000),
            ];
        });
    }

}
