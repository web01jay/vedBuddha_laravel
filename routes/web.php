<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/artisan/{command}', function(string $command) {
	$rawOutput = new BufferedOutput();
	Artisan::call($command, [], $rawOutput);

	return nl2br($rawOutput->fetch());
});