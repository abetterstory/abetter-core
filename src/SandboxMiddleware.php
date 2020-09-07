<?php

namespace ABetter\Core;

use Closure;

class SandboxMiddleware {

	public function handle($request, Closure $next) {

		if (env('APP_ENV') == 'sandbox' || isset($_GET['clearcache'])) {
			_deleteFiles(app('path.storage').'/framework/views/',FALSE);
		}

		if (isset($_GET['clearcache'])) {
			_deleteFiles(app('path.storage').'/cache/',FALSE);
		}

		if (!in_array(strtolower(env('APP_ENV')),['production','stage'])) {
			app()->register('ABetter\Core\PhpConsoleServiceProvider');
		}

		return $next($request);

	}

}
