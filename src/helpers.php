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
