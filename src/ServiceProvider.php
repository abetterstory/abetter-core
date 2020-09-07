<?php

namespace ABetter\Core;

use ABetter\Core\SandboxMiddleware;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

    public function boot() {

		$this->app->make(Kernel::class)->pushMiddleware(SandboxMiddleware::class);

		view()->composer('*', function($view){
			view()->share('view', (object) [
				'origin' => __CLASS__,
				'name' => $view->getName(),
				'path' => $view->getPath(),
			]);
		});

    }

    public function register() {
		//
    }

}
