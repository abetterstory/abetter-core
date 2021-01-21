<?php

namespace ABetter\Core;

use ABetter\Core\SandboxMiddleware;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {

    public function boot() {

		$this->app->make(Kernel::class)->pushMiddleware(Middleware::class);
		$this->app->make(Kernel::class)->pushMiddleware(SandboxMiddleware::class);

		view()->composer('*', function($view){
			global $view_data; $view_data = $view_data ?? [];
			if (!preg_match('/__components::/',$view->getName())) $view_data[] = [
				'provider' => __CLASS__,
				'name' => $view->getName(),
				'path' => dirname($view->getPath()),
				'file' => basename($view->getPath()),
				'data' => $view->getData(),
			];
			view()->share('view', $view_data);
		});

		$this->loadViewsFrom(__DIR__.'/../views', 'abetter');

		$this->loadViewComponentsAs('', [
			ContainerComponent::class,
	    ]);

    }

    public function register() {
		//
    }

}
