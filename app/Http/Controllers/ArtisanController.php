<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class ArtisanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($request->get('key') !== config('constants.artisan_route_key')) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    public function runCommand(string $command): string
    {
        $rawOutput = new BufferedOutput();
        Artisan::call($command, [], $rawOutput);

        return nl2br($rawOutput->fetch());
    }
}
