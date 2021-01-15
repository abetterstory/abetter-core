<?php

namespace ABetter\Core;

use Illuminate\Support\Facades\Route;

class Service {

	public $name;
	public $format;
	public $cache;
	public $lock;
	public $data;

	public $default = [
		'cors' => '',
		'cache' => '5 minutes',
		'lock' => 0,
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
		$this->origin = $_GET['origin'] ?? 'direct';
		$this->domain = env('APP_CANONICAL', env('APP_URL'));
		$this->uri = strtok($_SERVER['REQUEST_URI'],'?#');
		$this->extension = pathinfo($this->uri, PATHINFO_EXTENSION);
		$this->query = explode('?',$_SERVER['REQUEST_URI'])[1] ?? '';
		$this->name = $this->opt['name'] ?? pathinfo($this->uri, PATHINFO_FILENAME);
		$this->format = $this->opt['format'] ?? self::format($this->extension);
		$this->cors = $this->opt['cors'] ?? '';
		$this->view = $this->opt['view'] ?? NULL;
		$this->data = $this->opt['data'] ?? $this->data;
		$this->cache = (is_numeric($this->opt['cache'])) ? (int) $this->opt['cache'] : strtotime($this->opt['cache'],0);
		$this->lock = (is_numeric($this->opt['lock'])) ? (int) $this->opt['lock'] : strtotime($this->opt['lock'],0);
		$this->build();
		$this->headers();
	}

	// ---

	public function build() {

		//

	}

	// ---

	public function data($data) {
		$this->data = $data;
	}

	// ---

	public function echo() {
		echo $this->response();
	}

	public function headers() {
		// Pass to Core Middleware
		$GLOBALS['HEADERS']['format'] = $this->format;
		$GLOBALS['HEADERS']['expire'] = $this->cache;
		$GLOBALS['HEADERS']['cors'] = $this->cors;
	}

	public function response() {
		if (is_array($this->data) || preg_match('/json/',$this->format)) {
			return response()->json($this->data)->content();
		}
		return response($this->data)->content();
    }

	// ---

	public function options($args) {
		$opt = $this->default ?? [];
		if (is_array($args[1]??NULL)) {
			$opt = array_merge($opt,$args[1]);
		} else if (is_array($args[0]??NULL)) {
			$opt = array_merge($opt,$args[0]);
		} else if (is_string($args[0])) {
			$opt['name'] = $args[0];
		}
		return $opt;
	}

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
		$formats = ($reverse) ? array_reverse($formats) : $formats;
		return $formats[$ext] ?? reset($formats);
	}

	// ---





	/*

	public $route = "";
	public $service = "";
	public $method = "";
	public $origin = "";
	public $slug = "";
	public $type = "";
	public $args = [];
	public $argx = [];
	public $query = [];
	public $data = [];
	public $file = NULL;
	public $expire = '1 hour';
	public $lockexpire = '2 minutes';
	public $md5expire = '2 hours';
	public $storage = 'service';
	public $response = NULL;
	public $handled = NULL;
	public $debug = NULL;
	public $log = [];
	public $logfile = FALSE;

	public $aws = [];
	public $invalidated = [];

	// ---

	public function __construct() {
		$this->boot(func_get_args());
	}

	public function __toString() {
		return (string) $this->response();
	}

	public function boot() {
		$this->args = func_get_args();
		$this->query = $_GET ?? [];
		$this->route = Route::getFacadeRoot()->current();
		$this->origin = $_GET['origin'] ?? 'direct';
		$this->service = _slugify(strtok($this->route->uri(),'{'));
		$this->method = trim($this->args[0]['method'] ?? $this->route->parameters['path'] ?? '', '/');
		$this->type = trim($this->args[0]['type'] ?? $this->route->parameters['type'] ?? '', '.');
		$this->slug = _slugify("{$this->service}-{$this->method}");
		$this->storage = storage_path($this->storage);
		if (!is_dir($this->storage)) \File::makeDirectory($this->storage,0777,TRUE);
		if ($this->service == 'service') {
			$this->argx = explode('/',$this->method);
			$this->service = $this->argx[0] ?? '';
			$this->method = $this->argx[1] ?? '';
		}
		$this->data = [
			'requested' => date(\DateTime::ISO8601),
			'origin' => $this->origin,
			'service' => $this->service,
			'method' => $this->method,
			'type' => $this->type
		];
		if ($this->locked()) {
			$this->data['locked'] = TRUE;
		}
		if (isset($_GET['debug'])) {
			$this->debug = TRUE;
			$this->data['debug'] = $this->debug;
		}
		$this->handle();
		$this->output();
		$this->logfile();
	}



	// ---

	public function logfile() {
		if (!$this->logfile) return;
		$file = $this->storage.'/'.($name ?? $this->slug).'.log';
		$log = json_encode($this->data);
		@file_put_contents($file, $log.PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	// ---

	public function log($key,$value=NULL) {
		if (!$value) $this->log[] = $key; else $this->log[$key] = $value;
	}

	public function response() {
		if ($this->debug) {
			$this->debug();
			$this->data['log'] = $this->log;
		}
		return _echoJson($this->data,$this->expire);
    }

	public function echo() {
		echo $this->response();
    }

	// ---

	public function locked($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		if (!$lock = (is_file($file)) ? file_get_contents($file) : NULL) return FALSE;
		if (strtotime($lock) > time()) return TRUE;
		$this->unlock($name);
		return FALSE;
	}

	public function lock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		$expire = (is_string($this->lockexpire)) ? strtotime('+'.$this->lockexpire) : time()+(int)$this->lockexpire;
		@file_put_contents($file,date(\DateTime::ISO8601,$expire));
	}

	public function unlock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		@unlink($file);
	}

	// ---

	public function md5changed($string) {
		if (isset($this->data['changed'])) return $this->data['changed'];
		$md5 = md5($string);
		$md5_last = $this->md5stored();
		$md5_changed = ($md5 === $md5_last) ? FALSE : TRUE;
		$this->data['changed'] = $md5_changed;
		if (!$md5_changed) return FALSE;
		$this->md5change($md5);
		return TRUE;
	}

	public function md5stored($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		if (!$md5 = (is_file($file)) ? file_get_contents($file) : NULL) return NULL;
		list($time,$value) = explode('=',$md5);
		if (strtotime($time) > time()) return $value;
		return NULL;
	}

	public function md5change($value="",$name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		$expire = (is_string($this->md5expire)) ? strtotime('+'.$this->md5expire) : time()+(int)$this->md5expire;
		@file_put_contents($file,date(\DateTime::ISO8601,$expire)."=".$value);
	}

	public function md5unlock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		@unlink($file);
	}

	// ---

	*/

}
