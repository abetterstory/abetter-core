<?php

namespace ABetter\Core;

use Illuminate\Support\Facades\Route;

class Service {

	public $name;
	public $method;
	public $format;
	public $cache;
	public $lock;
	public $md5;
	public $error;
	public $data;
	public $content;
	public $log;
	public $json;
	public $debug;

	public $default = [
		'cors' => FALSE,
		'cors_origin' => '*',
		'cache' => TRUE,
		'cache_expire' => 0,
		'lock' => FALSE,
		'lock_expire' => '5 minutes',
		'md5' => TRUE,
		'md5_expire' => '2 hours',
	];

	public function __construct() {
		$this->boot(...func_get_args());
	}

	public function __toString() {
		return (string) $this->response();
	}

	public function boot() {
		$this->args = func_get_args();
		$this->opt = $this->options($this->args);
		$this->debug = (isset($_GET['debug'])) ? TRUE : ($this->opt['debug'] ?? FALSE);
		$this->origin = $_GET['origin'] ?? 'direct';
		$this->domain = env('APP_CANONICAL', env('APP_URL'));
		$this->uri = strtok($_SERVER['REQUEST_URI'],'?#');
		$this->extension = pathinfo($this->uri, PATHINFO_EXTENSION);
		$this->query = explode('?',$_SERVER['REQUEST_URI'])[1] ?? '';
		$this->storage = storage_path($this->opt['storage'] ?? 'service');
		if (!is_dir($this->storage)) \File::makeDirectory($this->storage,0777,TRUE);
		// ---
		$this->name = _slugify($this->opt['name'] ?? pathinfo($this->uri, PATHINFO_FILENAME));
		$this->method = $this->opt['method'] ?? '';
		$this->format = $this->opt['format'] ?? self::format($this->extension);
		$this->view = $this->opt['view'] ?? NULL;
		$this->error = NULL;
		// ---
		$this->cors = (boolean) $this->opt['cors'];
		$this->cors_origin = (is_string($this->opt['cors'])) ? $this->opt['cors'] : $this->opt['cors_origin'];
		// ---
		$this->cache = (boolean) $this->opt['cache'];
		$this->cache_expire = $this->time((is_string($this->opt['cache']) || is_numeric($this->opt['cache'])) ? $this->opt['cache'] : $this->opt['cache_expire']);
		// ---
		$this->md5 = (boolean) $this->opt['md5'];
		$this->md5_expire = $this->time((is_string($this->opt['md5']) || is_numeric($this->opt['md5'])) ? $this->opt['md5'] : $this->opt['md5_expire']);
		$this->md5_file = $this->storage.'/'.$this->name.'.md5';
		$this->md5_changed = NULL;
		if (!$this->md5) $this->md5_unlock();
		// ---
		$this->lock = (boolean) $this->opt['lock'];
		$this->lock_expire = $this->time((is_string($this->opt['lock']) || is_numeric($this->opt['lock'])) ? $this->opt['lock'] : $this->opt['lock_expire']);
		$this->lock_file = $this->storage.'/'.$this->name.'.lock';
		// ---
		$this->content = $this->opt['content'] ?? "";
		$this->data = $this->opt['data'] ?? [];
		$this->log = $this->opt['log'] ?? [];
		$this->json = $this->opt['json'] ?? [
			'status' => 'ok',
			'message' => 'success',
			'requested' => date(\DateTime::ISO8601),
			'origin' => $this->origin,
			'service' => $this->name,
			'method' => $this->method,
			'data' => [],
			'log' => [],
		];
		// ---
		$this->headers();
		if ($this->lock) {
			if ($this->locked()) {
				$this->error('locked');
				return $this->response();
			} else {
				$this->lock();
			}
		}
		// ---
		$this->build();
	}


	// ---

	public function build() {

		//sleep(1);

	}

	// ---

	public function data(array $data) {
		if (!is_array($data)) return;
		$this->data = $data;
		$this->json['data'] = $this->data;
		return $this;
	}

	public function content(string $content) {
		if (!is_string($content)) return;
		$this->content = $content;
		$this->json['content'] = $this->content;
		return $this;
	}

	public function message($message="") {
		$this->json['message'] = (string) $message;
		return $this;
	}

	public function pass($message="") {
		$this->json['status'] = 'pass';
		$this->json['message'] = (string) $message;
		return $this;
	}

	public function success($message="") {
		$this->json['status'] = 'success';
		$this->json['message'] = (string) $message;
		return $this;
	}

	public function fail($message="") {
		$this->json['status'] = 'fail';
		$this->json['message'] = (string) $message;
		return $this;
	}

	public function error($message="") {
		$this->json['status'] = 'error';
		$this->json['message'] = (string) $message;
		return $this;
	}

	public function log($key,$val=NULL) {
		if (is_string($key) && !empty($val)) {
			$add = [$key => $val];
		} else if (is_array($key)) {
			$add = $key;
		} else if (is_string($key)) {
			$add = [$key];
		}
		$this->json['log'] = array_merge($this->json['log'],$add);
		return $this;
	}

	// ---

	public function locked() {
		if (!$lock = (is_file($this->lock_file)) ? @file_get_contents($this->lock_file) : NULL) return FALSE;
		if (strtotime($lock) > time()) return TRUE;
		$this->unlock();
		return FALSE;
	}

	public function lock() {
		if (empty($this->lock)) return;
		$expire = (is_string($this->lock_expire)) ? strtotime('+'.$this->lock_expire) : time()+(int)$this->lock_expire;
		@file_put_contents($this->lock_file,date(\DateTime::ISO8601,$expire));
	}

	public function unlock() {
		@unlink($this->lock_file);
	}

	// ---

	public function changed($string) {
		return $this->md5_changed($string);
	}

	public function unchanged($string) {
		return !$this->md5_changed($string);
	}

	public function md5_changed($string) {
		if (empty($this->md5)) {
			$this->md5_changed = 'n/a';
			return $this->md5_changed;
		}
		$md5 = md5((is_string($string)) ? $string : @json_encode($string));
		$stored = $this->md5_stored();
		if ($this->md5_changed = ($md5 === $stored) ? FALSE : TRUE) {
			$this->md5_store($md5);
		}
		return $this->md5_changed;
	}

	public function md5_store($hash) {
		$expire = (is_string($this->md5_expire)) ? strtotime('+'.$this->md5_expire) : time()+(int)$this->md5_expire;
		@file_put_contents($this->md5_file,date(\DateTime::ISO8601,$expire)."=".$hash);
	}

	public function md5_stored() {
		$md5 = (is_file($this->md5_file)) ? @file_get_contents($this->md5_file) : "=";
		list($time,$hash) = explode('=',$md5);
		return (strtotime($time) > time()) ? $hash : NULL;
	}

	public function md5_unlock() {
		@unlink($this->md5_file);
	}

	// ---

	public function exit() {
		echo $this->response();
		exit;
	}

	public function echo() {
		echo $this->response();
	}

	public function headers() {
		// Pass to Core Middleware
		$GLOBALS['HEADERS']['format'] = $this->format;
		if ($this->cache) $GLOBALS['HEADERS']['expire'] = $this->cache_expire;
		if ($this->cors) $GLOBALS['HEADERS']['cors'] = $this->cors_origin;
	}

	public function response() {
		$this->unlock();
		if (!preg_match('/json/',$this->format)) {
			return response($this->content)->content();
		}
		if (empty($this->content)) unset($this->json['content']);
		if (empty($this->debug)) unset($this->json['log']);
		return response()->json($this->json)->content();
    }

	// ---

	public function time($time) {
		return (is_numeric($time)) ? (int) $time : strtotime($time,0);
	}

	public function options($args) {
		$opt = $this->default ?? [];
		if (is_array($args[1]??NULL)) {
			$opt = array_merge($opt,$args[1]);
		} else if (is_array($args[0]??NULL)) {
			$opt = array_merge($opt,$args[0]);
		}
		if (is_string($args[0])) {
			$opt['name'] = $args[0];
		}
		return $opt;
	}

	// ---

	public static function format($ext,$reverse=FALSE) {
		$formats = [
			'json' => 'application/json',
			'pdf' => 'application/pdf',
			'js' => 'application/javascript',
			'txt' => 'text/plain',
			'html' => 'text/html',
			'xml' => 'text/xml',
			'css' => 'text/css',
			'svg' => 'image/svg+xml',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'ico' => 'image/x-icon',
			'ttf' => 'application/x-font-ttf',
			'eot' => 'application/vnd.ms-fontobject',
			'woff' => 'application/font-woff',
			'woff2' => 'application/font-woff2',
			'm4v' => 'video/mp4',
			'mp4' => 'video/mp4',
			'ogv' => 'video/ogg',
			'ogg' => 'video/ogg',
			'webm' => 'video/webm',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		];
		$formats = ($reverse) ? array_flip($formats) : $formats;
		return $formats[$ext] ?? reset($formats);
	}

}
