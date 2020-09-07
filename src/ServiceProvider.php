<?php

namespace ABetter\Core;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {

		view()->composer('*', function($view){
			view()->share('view', (object) [
				'origin' => __CLASS__,
				'name' => $view->getName(),
				'path' => $view->getPath(),
			]);
		});

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register() {
		//
    }

}
