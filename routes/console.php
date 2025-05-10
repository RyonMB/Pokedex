<?php


use App\Jobs\BatchPokemonJobs;
use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-batches')->daily();
Schedule::job(new BatchPokemonJobs)->everyFiveMinutes();
