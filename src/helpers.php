<?php

if (!function_exists('_log')) {

	function _log($var=NULL,$label=NULL,$ch=NULL) {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		if (!empty($label) && is_string($var) && !is_string($label)) {
			$switch = $var; $var = $label; $label = $switch;
		}
		if (class_exists('\Debugbar') && (!$ch || $ch == 'debugbar')) {
			\Debugbar::addMessage($var,$label);
		}
		if (class_exists('\PhpConsole\Connector') && (!$ch || $ch == 'phpconsole')) {
			\PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var,$label,1);
		}
	}

	if (!function_exists('_console')) {
		function _console($var=NULL,$label=NULL) {
			return _log($var,$label,'phpconsole');
		}
	}

	if (!function_exists('_debug')) {
		function _debug($var=NULL,$label=NULL) {
			return _log($var,$label,'debugbar');
		}
	}

}

// ---

if (!function_exists('_deleteFiles')) {

	function _deleteFiles($path,$rmdir=TRUE) {
		$i = new \DirectoryIterator($path);
        foreach ($i AS $f) {
			if ($f->isFile() && !preg_match('/^\./',$f->getFilename())) {
                @unlink($f->getRealPath());
            } else if (!$f->isDot() && $f->isDir()) {
                _deleteFiles($f->getRealPath());
            }
        }
        if ($rmdir) @rmdir($path);
	}

}
