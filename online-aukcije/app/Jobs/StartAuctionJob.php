<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Aukcija;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartAuctionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $aukcija;

    /**
     * Create a new job instance.
     */
    public function __construct(Aukcija $aukcija)
    {
        $this->aukcija= $aukcija;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->aukcija->status_aukcije === 'predstojeca') {
            $this->aukcija->status_aukcije = 'aktivna';
            $this->aukcija->save();
        }
    }
}
