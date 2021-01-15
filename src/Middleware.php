<?php

namespace ABetter\Core;

use Closure;

class Middleware {

	public function handle($request, Closure $next) {

		$response = $next($request);

		// ---

		if (($_HEADERS = $GLOBALS['HEADERS'] ?? NULL) && !isset($_GET['debug']) && method_exists($response,'header')) {

			// Redirect
			if (($redirect = $_HEADERS['redirect'] ?? '')) {
				return \Redirect::to($redirect);
			}

			// Error
			if (($error = $_HEADERS['error'] ?? 0) && $error > 400) {
				$response->setStatusCode($error);
			}

			// Format
			if (($format = $_HEADERS['format'] ?? '')) {
				$response->header('Content-Type', $format);
			}

			// Cache
			if (isset($_HEADERS['expire'])) {
				$expire = ($error > 400) ? 300 : 2628000; // Default 1 month
				if ($_HEADERS['expire'] !== '') {
					$expire = (is_numeric($_HEADERS['expire'])) ? (int) $_HEADERS['expire'] : strtotime($_HEADERS['expire'],0);
				}
				if ($expire > 0) {
					$response->header('Pragma', 'public');
					$response->header('Cache-Control', 'public, max-age='.$expire);
					$response->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $expire));
					$response->setEtag(md5($response->content()));
				} else {
					$response->header('Pragma', 'no-cache');
					$response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
					$response->header('Expires', '0');
				}
			}

			// Cors
			if (($cors = $_HEADERS['cors'] ?? '')) {
				$response->header('Access-Control-Allow-Origin', '*');
				$response->header('Access-Control-Allow-Headers', 'origin, x-requested-with, content-type');
				$response->header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
			}

		}

		// ---

		return $response;

	}

}
