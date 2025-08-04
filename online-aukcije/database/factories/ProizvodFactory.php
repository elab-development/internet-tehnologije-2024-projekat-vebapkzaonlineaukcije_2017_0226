<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proizvod;
use App\Models\Aukcija;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proizvod>
 */
class ProizvodFactory extends Factory
{
    protected $model = Proizvod::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naziv' => $this->faker->sentence(3, true),
            'opis' => $this->faker->paragraph(),
            'kategorija' => $this->faker->randomElement([
                'elektronika','knjige',
                'odeca','umetnost',
                'sport','kucni aparati',
                'namestaj','vozila',
                'alati','kucni ljubimci','ostalo'
            ]),
            'stanje' => $this->faker->randomElement([
                'novo','kao novo','korisceno','osteceno'     
            ]),
            'slika_url' => $this->faker->imageUrl(640, 480, 'products', true),
            'aukcija_id' => Aukcija::factory()
        ];
    }
}
