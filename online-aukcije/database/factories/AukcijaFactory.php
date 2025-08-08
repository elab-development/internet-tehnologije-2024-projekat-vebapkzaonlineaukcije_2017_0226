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
        // Generišemo datum početka u bliskoj budućnosti (na primer, 1-3 dana od sada)
        $datumPocetka = Carbon::now()->addDays(rand(1, 7))->addSeconds(rand(0, 86400));
        
        $pocetnaCena = $this->faker->numberBetween(100, 500000);
        $maksimalnaCena = $this->faker->optional()->numberBetween($pocetnaCena + 1, 1000000);

        return [
            'naziv' => $this->faker->sentence(2),
            'pocetna_cena' => $pocetnaCena,
            'trenutna_cena' => null, // Uvek null za novu aukciju
            'maksimalna_cena' => $maksimalnaCena,
            'datum_pocetka' => $datumPocetka,
            'status_aukcije' => 'predstojeca', // Uvek predstojeca za novu aukciju
            'vreme_isteka' => $datumPocetka->copy()->addSeconds(30), // Dodajemo 30 sekundi na datum početka
        ];
    }

    public function aktivna(): Factory
    {
        return $this->state(function (array $attributes) {
            $pocetnaCena = $attributes['pocetna_cena'];
            $datumPocetka = Carbon::now()->subSeconds(rand(0, 86400));
            $trenutnaCena = $this->faker->optional(0.7)->numberBetween($pocetnaCena, $pocetnaCena * 2);
            $vremeIsteka = Carbon::now()->addSeconds(rand(1, 30));

            return [
                'datum_pocetka' => $datumPocetka,
                'status_aukcije' => 'aktivna',
                'trenutna_cena' => $trenutnaCena,
                'vreme_isteka' => $vremeIsteka,
            ];
        });

    }

        public function zavrsena(): Factory
    {
        return $this->state(function (array $attributes) {
            $pocetnaCena = $attributes['pocetna_cena'];
            $datumPocetka = Carbon::now()->subDays(rand(1, 7))->subSeconds(rand(0, 86400));
            $trajanjeAukcije = rand(30, 86400);
            $vremeIsteka = $datumPocetka->copy()->addSeconds($trajanjeAukcije);

            if ($vremeIsteka > Carbon::now()) {
                $vremeIsteka = Carbon::now()->subSeconds(rand(1, 10));
            }

            $trenutnaCena = $this->faker->optional(0.7)->numberBetween($pocetnaCena, $pocetnaCena * 2);

            return [
                'datum_pocetka' => $datumPocetka,
                'status_aukcije' => 'zavrsena',
                'trenutna_cena' => $trenutnaCena,
                'vreme_isteka' => $vremeIsteka,
            ];
        });
    }
}
