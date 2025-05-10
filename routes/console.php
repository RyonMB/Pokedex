<?php


use Illuminate\Support\Facades\Schedule;
use App\Jobs\BatchPokemonJobs;

Schedule::command('queue:prune-batches')->daily();
Schedule::job(new BatchPokemonJobs())->everyFiveMinutes();


