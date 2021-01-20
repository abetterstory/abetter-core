@php

$Xcloak = (object) $data ?? NULL;
$Xcloak->slot = $slot ?? "";
$Xcloak->class = "";
$Xcloak->attr = "";
foreach ($Xcloak->attributes??[] AS $key => $val) {
	switch ($key) {
		case 'class' : $Xcloak->class .= " {$val}"; break;
		default : $Xcloak->attr .= " $key=\"{$val}\"";
	}
}

@endphp

<div class="component--x-cloak {{ $Xcloak->class }}" {{ $Xcloak->attr }} x-cloak>
	<div class="--x-cloaked">
		{{ $Xcloak->slot }}
	</div>

<x-script>
(function(){

	var $this = this,
		$w = window,
		$d = document,
		$b = $d.documentElement;

	$this.xcloak = function(e) {

	    var $els = $d.querySelectorAll('[x-cloak]'); // IE breaks with '--';
		[].forEach.call($els,function($el){

	        var rect = $el.getBoundingClientRect();
			var cl = $el.classList;

	        if (rect.top < $w.innerHeight && rect.bottom > 0) {

				if (!cl.contains('--enter')) {
					$this.xcall($el,'onenter');
					cl.add('--enter');
				};

				if ($this.dir < 0) {
					cl.add('--reverse');
				};

				cl.remove('--leave');

	        } else {

				if (cl.contains('--enter')) {
					$this.xcall($el,'onleave');
					cl.add('--leave');
				};

				cl.remove('--enter','--reverse');

			};

	    });
	};

	$this.xcall = function($el,cb) {
		var func = ($el.getAttribute(cb)||'').replace(/^\"([^\"\(]+).*/,'$1'); if (!func) return;
		if (func && (eval('typeof('+func+') == typeof(Function)'))) {
			return window[func]($el);
		};
	};

	// ---

	$this.dir = 0;
	$this.s = 0;
	$this.xscrd = function() {
		$this.dir  = 0;
		if ($this.s > $b.scrollTop) {
			$this.dir = -1;
		} else if ($this.s < $b.scrollTop) {
			$this.dir = 1;
		}
		$this.s = $b.scrollTop;
	};

	$w.addEventListener('scroll', $this.xscrd);
	$w.addEventListener('scroll', $this.xcloak);
	$w.addEventListener('load', $this.xcloak);
	$w.addEventListener('resize', $this.xcloak);

})();
</x-script>

</div>
