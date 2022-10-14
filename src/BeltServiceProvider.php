<?php

namespace Uzbek\Belt;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeltServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('belt')->hasConfigFile();

        $this->app->singleton(Belt::class, function () {
            $config = config('belt');
            Http::macro('belt', function ($method, $url, $data = []) use ($config) {
                $url = $config['base_url'].$url;

                return Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->withToken($config['token'])->$method($url, $data);
            });
        });
    }
}
