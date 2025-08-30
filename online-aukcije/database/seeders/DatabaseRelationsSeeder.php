<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Korisnik;
use App\Models\Aukcija;
use App\Models\Proizvod;
use App\Models\Ponuda;
use Faker\Factory as Faker;

class DatabaseRelationsSeeder extends Seeder
{
    
    public function run(): void
    {
        $faker = Faker::create();

        $korisnici = Korisnik::factory()->count(10)->create();
        $this->command->info('Kreirano 10 korisnika.');

        $predstojeceAukcije = Aukcija::factory()->count(10)->create();
        $this->command->info('Kreirano 10 predstojećih aukcija.');

        $zavrseneAukcije = Aukcija::factory()->zavrsena()->count(10)->create();
        $this->command->info('Kreirano 10 završenih aukcija.');
        
        $sveAukcije = $predstojeceAukcije->merge($zavrseneAukcije);

        $sveAukcije->each(function ($aukcija) use ($faker) {
            Proizvod::factory()->count($faker->numberBetween(1, 3))->create([
                'aukcija_id' => $aukcija->id,
            ]);
            $this->command->info("Aukcija ID: {$aukcija->id} - dodeljeni proizvodi.");
        });

        $zavrseneAukcije->each(function ($aukcija) use ($korisnici, $faker) {
            $ponudaCount = $faker->numberBetween(0, 5);
            if ($ponudaCount > 0) {
                 for ($i = 0; $i < $ponudaCount; $i++) {
                    $randomKorisnik = $korisnici->random();
                    $ponudaIznos = ($aukcija->trenutna_cena ?? $aukcija->pocetna_cena) + $faker->numberBetween(1, 100);
                    
                    Ponuda::factory()->forAukcija($aukcija)->create([
                        'korisnik_id' => $randomKorisnik->id,
                        'iznos' => $ponudaIznos
                    ]);
                    $aukcija->trenutna_cena = $ponudaIznos;
                    $aukcija->save();
                }
                $this->command->info("Aukcija ID: {$aukcija->id} - dodeljeno {$ponudaCount} ponuda. 
                Finalna cena: {$aukcija->trenutna_cena}.");
            } else {
                $this->command->info("Aukcija ID: {$aukcija->id} - bez ponuda.");
            }
        });


        $this->command->info('Svi podaci uspešno generisani i povezani!');
    }
}
