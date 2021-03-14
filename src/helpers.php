<?php

if (!function_exists('_slugify')) {
	function _slugify($url,$sep='-',$base=FALSE) {
		$url = ($base) ? basename($url) : preg_replace('/(\/|\?|\=|\&|\#)/',$sep,$url);
		$ext = strtolower(pathinfo($url,PATHINFO_EXTENSION));
		$name = strtolower(pathinfo($url,PATHINFO_FILENAME));
		$slug = Illuminate\Support\Str::slug($name,$sep);
		if (!$slug) $slug = "na{$sep}".Illuminate\Support\Str::random();
		if ($ext) $slug .= ".{$ext}";
		return $slug;
	}
}

if (!function_exists('_boolean')) {
	function _boolean($string) {
		$boolean = ($string === NULL) ? NULL : (boolean) $string;
		if (is_string($string) && strtolower($string) === 'false') $boolean = FALSE;
		return $boolean;
	}
}

// ---

if (!function_exists('_is_debug')) {
	function _is_debug() {
		return (env('APP_DEBUG')) ? TRUE : FALSE;
	}
}

if (!function_exists('_is_sandbox')) {
	function _is_sandbox() {
		return (in_array(strtolower(env('APP_ENV')),['sandbox'])) ? TRUE : FALSE;
	}
}

if (!function_exists('_is_local')) {
	function _is_local() {
		return (in_array(strtolower(env('APP_ENV')),['local','sandbox'])) ? TRUE : FALSE;
	}
}

if (!function_exists('_is_dev')) {
	function _is_dev() {
		return (in_array(strtolower(env('APP_ENV')),['stage','production'])) ? FALSE : TRUE;
	}
}

if (!function_exists('_is_live')) {
	function _is_live() {
		return (in_array(strtolower(env('APP_ENV')),['stage','production'])) ? TRUE : FALSE;
	}
}

if (!function_exists('_is_stage')) {
	function _is_stage() {
		return (strtolower(env('APP_ENV')) == 'stage') ? TRUE : FALSE;
	}
}

if (!function_exists('_is_production')) {
	function _is_production() {
		return (strtolower(env('APP_ENV')) == 'production') ? TRUE : FALSE;
	}
}

if (!function_exists('_is_prod')) {
	function _is_prod() {
		return _is_production();
	}
}

// ---

if (!function_exists('_cache_headers')) {
	function _cache_headers($options) {
		$headers = [
			'X-ABetter' => 'core',
		];
		// ---
		if (($format = $options['format'] ?? '')) {
			$headers['Content-Type'] = $format;
		}
		// ---
		if (($error = $options['error'] ?? 0) && $error > 400) {
			$headers['Status Code'] = $error;
		}
		// ---
		if (isset($options['expire'])) {
			$expire = ($error > 400) ? 300 : 2628000; // Default 1 month
			$modified = time();
			if ($options['expire'] !== '') {
				$expire = (is_numeric($options['expire'])) ? (int) $options['expire'] : strtotime($options['expire'],0);
			}
			if (is_numeric($options['modified']??NULL)) {
				$modified = (int) $options['modified'];
			}
			if ($expire > 0) {
				$headers['Pragma'] = 'public';
				$headers['Cache-Control'] = 'public, max-age='.$expire;
				$headers['Expires'] = gmdate('D, d M Y H:i:s \G\M\T', $modified + $expire);
				$options['etag'] = $options['etag'] ?? 'W/"'.$modified.'"';
			} else {
				$headers['Pragma'] = 'no-cache';
				$headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';
				$headers['Expires'] = '0';
			}
		}
		// ---
		if (($etag = $options['etag'] ?? '')) {
			$headers['ETag'] = $etag;
		}
		// ---
		if (($format = $options['cors'] ?? '')) {
			$headers['Access-Control-Allow-Origin'] = '*';
			$headers['Access-Control-Allow-Headers'] = 'origin, x-requested-with, content-type';
			$headers['Access-Control-Allow-Methods'] = 'PUT, GET, POST, DELETE, OPTIONS';
		}
		// ---
		return $headers;
	}
}
