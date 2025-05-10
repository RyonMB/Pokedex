<?php

namespace App\Jobs;

use App\Models\Pokemon;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;

final class BatchPokemonJobs implements ShouldQueue
{
    use Queueable;

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('pokemon-batch'))->expireAfter(300)->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Create an empty batch with callback for when chunks complete
        $batch = Bus::batch([])
            ->allowFailures()
            ->name('Pokemon Batch - '.Carbon::now()->toDayDateTimeString())
            ->dispatch();

        // Use cursor for memory efficiency, map to jobs, chunk and add to batch
        Pokemon::query()
            ->where('has_changed', true)
            ->cursor()
            ->map(fn (Pokemon $pokemon): PushPokemonJob => new PushPokemonJob($pokemon))
            ->chunk(100)
            ->each(function (LazyCollection $jobs) use ($batch): void {
                $batch->add($jobs->toArray());
            });
    }
}
