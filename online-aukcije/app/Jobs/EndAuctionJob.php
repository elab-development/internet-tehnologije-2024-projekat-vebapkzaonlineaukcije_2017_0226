<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Aukcija;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        if ($this->aukcija->status_aukcije === 'aktivna') {
            $this->aukcija->status_aukcije = 'zavrsena';
            $this->aukcija->save();
        }
    }
}
