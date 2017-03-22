<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot() {
        // Using class based composers...
        view()->composer ( 'frontend.user.layouts.master', 'App\Http\ViewComposers\MasterComposer' );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //
    }
}
