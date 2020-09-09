<?php

if (!function_exists('_log')) {
	function _log($label=NULL,$var=NULL,$ch=NULL) {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		if (empty($var)) {
			$var = $label; $label = NULL;
		}
		if (class_exists('\Debugbar') && (!$ch || $ch == 'debugbar')) {
			\Debugbar::addMessage($var,(string)$label);
		}
		if (class_exists('\PhpConsole\Connector') && (!$ch || $ch == 'phpconsole')) {
			global $_connector; if (empty($_connector)) {
				\PhpConsole\Connector::setPostponeStorage(new PhpConsole\Storage\File(storage_path('php-console.dat'),true));
				$_connector = TRUE;
			}
			\PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var,(string)$label,1);
		}
	}
	if (!function_exists('_console')) {
		function _console($label=NULL,$var=NULL) {
			return _log($label,$var,'phpconsole');
		}
	}
	if (!function_exists('_debug')) {
		function _debug($label=NULL,$var=NULL) {
			return _log($label,$var,'debugbar');
		}
	}
}

// ---

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

// ---

function _boolean($string) {
	$boolean = ($string === NULL) ? NULL : (boolean) $string;
	if (is_string($string) && strtolower($string) === 'false') $boolean = FALSE;
	return $boolean;
}
