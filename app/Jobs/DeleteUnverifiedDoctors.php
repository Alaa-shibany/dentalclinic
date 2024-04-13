<?php

namespace App\Jobs;

use App\Models\Doctor;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;

class DeleteUnverifiedDoctors
{
    use Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fiveMinutesAgo = now()->subMinutes(5);
        $doctors=Doctor::where('created_at', '<=', $fiveMinutesAgo)
        ->whereNull('phone_verified_at')
        ->get();
        foreach($doctors as $d){
            $d->deleteProfilePicture();
        }
        $count=$doctors->count();
        Log::info("Deleted $count doctors due to not verifying their accounts.");
    }
}
