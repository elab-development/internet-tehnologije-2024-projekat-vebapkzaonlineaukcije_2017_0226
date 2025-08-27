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
use App\Notifications\AuctionEndedWithWinner;
use App\Notifications\AuctionEndedWithoutBids;
use Carbon\Carbon;

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
        $svezaAukcija = Aukcija::find($this->aukcija->id);

        if (!$svezaAukcija || $svezaAukcija->status_aukcije !== 'aktivna') {
            return;
        }

        if (Carbon::now()->lt(Carbon::parse($svezaAukcija->vreme_isteka))) {
            Log::info("Aukcija ID {$svezaAukcija->id} je produzena. Ponovno zakazivanje job-a.");
            self::dispatch($svezaAukcija)->delay(Carbon::parse($svezaAukcija->vreme_isteka));
            return;
        }
        try {
            DB::transaction(function () use ($svezaAukcija){
                $aukcija = Aukcija::with(['korisnik', 'ponude.korisnik'])
                                  ->lockForUpdate()
                                  ->find($svezaAukcija->id);

                if (!$aukcija || $aukcija->status_aukcije !== 'aktivna' || Carbon::now()->lt($aukcija->vreme_isteka)) {
                    return;
                }

                $pobednickaPonuda = $aukcija->ponude->sortByDesc('iznos')->first();
                $kreator = $aukcija->korisnik;
                
                if (!$kreator) {
                    throw new \Exception("Kreator aukcije (ID: {$aukcija->id}) nije pronadjen.");
                }

                if ($pobednickaPonuda) {
                    $pobednik = Korisnik::lockForUpdate()->find($pobednickaPonuda->korisnik_id);

                    if (!$pobednik) {
                        $aukcija->status_aukcije = 'zavrsena';
                        $aukcija->save();
                        $kreator->notify(new AuctionEndedWithoutBids($aukcija));
                        return;
                    }

                    $iznosTransakcije = $pobednickaPonuda->iznos;

                    $pobednik->stanje_na_racunu -= $iznosTransakcije;
                    $pobednik->save();
                    
                    $kreator->stanje_na_racunu += $iznosTransakcije;
                    $kreator->save();

                    $pobednik->notify(new AuctionEndedWithWinner($aukcija, $pobednik, $kreator, $iznosTransakcije));
                    $kreator->notify(new AuctionEndedWithWinner($aukcija, $pobednik, $kreator, $iznosTransakcije));
                } else {
                    $kreator->notify(new AuctionEndedWithoutBids($aukcija));
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
