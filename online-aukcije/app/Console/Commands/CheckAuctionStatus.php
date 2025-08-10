<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aukcija;

class CheckAuctionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aukcije:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proverava statuse aukcije i azurira ih.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $predstojece_aukcije = Aukcija::where('status_aukcije', 'predstojeca')
            ->where('datum_pocetka', '<=', now())
            ->get();

        foreach ($predstojece_aukcije as $aukcija) {
            $aukcija->status_aukcije = 'aktivna';
            $aukcija->save();
            $this->info("Aukcija '{$aukcija->naziv}' (ID: {$aukcija->id}) je uspesno aktivirana.");
        }

        $aktivne_aukcije = Aukcija::where('status_aukcije', 'aktivna')
            ->where('vreme_isteka', '<=', now())
            ->get();

        foreach ($aktivne_aukcije as $aukcija) {
            $aukcija->status_aukcije = 'zavrsena';
            $aukcija->save();
            $this->info("Aukcija '{$aukcija->naziv}' (ID: {$aukcija->id}) je uspešno zavrsena.");
        }

        $this->info('Provera statusa aukcija je zavrsena.');

        return Command::SUCCESS;
    }
}
