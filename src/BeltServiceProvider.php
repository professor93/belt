<?php

namespace Uzbek\Belt;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BeltServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('belt')->hasConfigFile();

        $this->app->singleton(Belt::class, function () {
            $config = config('belt');

            return new Belt(
                base_url: $config['base_url'],
                username: $config['username'],
                password: $config['password'],
            );
        });
    }
}
