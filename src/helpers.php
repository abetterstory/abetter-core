<?php

if (!function_exists('_log')) {

	function _log($var=NULL,$label=NULL) {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		if (!empty($label) && is_string($var) && !is_string($label)) {
			$switch = $var; $var = $label; $label = $switch;
		}
		if (class_exists('\Debugbar')) {
			//\Debugbar::addMessage($var,$label);
		}
		//if (class_exists('\Clockwork\Support\Laravel\ClockworkServiceProvider')) {
			//clock($var,$label);
		//}
		\PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var,$label,1);
	}

}
