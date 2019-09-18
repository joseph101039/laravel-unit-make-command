<?php

namespace RDM\MakeUnitCommand;

use Illuminate\Support\ServiceProvider;

class UnitCommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__.'/generator.php' => config_path('generator.php'),
            __DIR__.'/stubs' => resource_path('stubs'),
            __DIR__.'/UnitMakeCommand.php' => app_path('Console/Commands/UnitMakeCommand.php'),
        ], 'generator');

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //

    }
}
