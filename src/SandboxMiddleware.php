<?php

namespace ABetter\Core;

use Closure;

class SandboxMiddleware {

	public function handle($request, Closure $next) {

		if (env('APP_SANDBOX') || env('APP_ENV') == 'sandbox' || isset($_GET['clearview'])) {
			$this->deleteFiles(app('path.storage').'/framework/views/',FALSE);
			$this->deleteFiles(app('path.bootstrap').'/cache/',FALSE);
		}

		if (isset($_GET['clearcache'])) {
			$this->deleteFiles(app('path.storage').'/cache/',FALSE);
		}

		return $next($request);

	}

	// ---

	protected function deleteFiles($path,$rmdir=TRUE) {
		if (!is_dir($path)) return;
		$i = new \DirectoryIterator($path);
        foreach ($i AS $f) {
			if ($f->isFile() && !preg_match('/^\./',$f->getFilename())) {
                @unlink($f->getRealPath());
            } else if (!$f->isDot() && $f->isDir()) {
                $this->deleteFiles($f->getRealPath());
            }
        }
        if ($rmdir) @rmdir($path);
	}

}
