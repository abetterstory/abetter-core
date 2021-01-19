@php

$Xcloak = (object) $data ?? NULL;
$Xcloak->slot = $slot ?? "";
$Xcloak->class = "";
$Xcloak->attr = "";
foreach ($Xcloak->attributes??[] AS $key => $val) {
	if ($key == "class") {
		$Xcloak->class .= " {$val}";
	} else {
		$Xcloak->attr .= " $key=\"{$val}\"";
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
	    var $e = $d.querySelectorAll('[x-cloak]'); // IE breaks with '--';
	    for (var i = 0; i < $e.length; i++) {
	        var rect = $e[i].getBoundingClientRect(), cl = $e[i].classList;
	        if (rect.top < $w.innerHeight && rect.bottom > 0) {
				cl.add('--enter');
				if ($this.dir < 0) {
					cl.add('--reverse');
				}
	        } else {
				cl.remove('--enter','--reverse');
			};
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
