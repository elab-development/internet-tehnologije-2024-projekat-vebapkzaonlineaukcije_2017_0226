<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Aukcija;
use App\Models\Korisnik;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EndAuctionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Aukcija $aukcija;

    /**
     * Create a new job instance.
     */
    public function __construct(Aukcija $aukcija)
    {
        $this->aukcija=$aukcija;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                $aukcija = Aukcija::with(['korisnik', 'ponude.korisnik'])
                                  ->lockForUpdate()
                                  ->find($this->aukcija->id);

                if (!$aukcija || $aukcija->status_aukcije !== 'aktivna') {
                    return;
                }

                $pobednickaPonuda = $aukcija->ponude->sortByDesc('iznos')->first();

                if ($pobednickaPonuda) {
                    $pobednik = Korisnik::lockForUpdate()->find($pobednickaPonuda->korisnik_id);
                    $kreator = $aukcija->korisnik;

                    $iznosTransakcije = $pobednickaPonuda->iznos;
                    
                    if (!$kreator) {
                         throw new \Exception("Kreator aukcije (ID: {$aukcija->id}) nije pronadjen.");
                    }

                    $pobednik->stanje_na_racunu -= $iznosTransakcije;
                    $pobednik->save();

                    $kreator->stanje_na_racunu += $iznosTransakcije;
                    $kreator->save();
                }

                $aukcija->status_aukcije = 'zavrsena';
                $aukcija->save();
            });
        } catch (\Exception $e) {
            Log::error("Greška prilikom završavanja aukcije ID {$this->aukcija->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
