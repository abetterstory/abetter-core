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
