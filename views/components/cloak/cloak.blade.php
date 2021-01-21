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

	var self = this,
		$w = window,
		$d = document,
		$b = $d.documentElement;

	self.xCloak = function(e) {

	    var els = $d.querySelectorAll('[x-cloak]'); // IE breaks with '--';
		[].forEach.call(els,function(el){

	        var rect = el.getBoundingClientRect(),
				cl = el.classList,
				cs = el.style,
				sp;

			cs.setProperty('--w', Math.round(rect.width));
			cs.setProperty('--h', Math.round(rect.height));

			sp = Math.round(((rect.height + $w.innerHeight) - rect.bottom) / (rect.height + $w.innerHeight) * 100) / 100;
			if (sp > 1) { sp = 1; } else if (sp < 0) { sp = 0; };

			cs.setProperty('--progress', sp);
			cs.setProperty('--dir', self.dir);

			// ---

	        if (rect.top < $w.innerHeight && rect.bottom > 0) {

				if (self.dir < 0) {
					cl.add('--reverse');
				};

				if (!cl.contains('--enter')) {
					self.xCall(el,'onenter');
					cl.add('--enter');
				};
				cl.remove('--leave');

	        } else {

				if (cl.contains('--enter')) {
					self.xCall(el,'onleave');
					cl.add('--leave');
				};
				cl.remove('--enter','--reverse','--focus','--blur');

			};

			// ---

			if (rect.top < ($w.innerHeight * 0.75) && (rect.bottom) > ($w.innerHeight * 0.25)) {

				if (!cl.contains('--focus')) {
					self.xCall(el,'onfocus');
					cl.add('--focus');
				};
				cl.remove('--blur');

			} else {

				if (cl.contains('--focus')) {
					self.xCall(el,'onblur');
					cl.add('--blur');
				};
				cl.remove('--focus');

			};

	    });
	};

	self.xCall = function(el,cb) {
		var func = (el.getAttribute(cb)||'').replace(/^\"([^\"\(]+).*/,'$1'); if (!func) return;
		if (func && (eval('typeof('+func+') == typeof(Function)'))) {
			return $w[func](el,cb);
		};
	};

	// ---

	self.dir = 0;
	self.s = 0;
	self.xDir = function() {
		self.dir  = 0;
		if (self.s > $b.scrollTop) {
			self.dir = -1;
		} else if (self.s < $b.scrollTop) {
			self.dir = 1;
		}
		self.s = $b.scrollTop;
	};

	$w.addEventListener('scroll', self.xDir);
	$w.addEventListener('scroll', self.xCloak);
	$w.addEventListener('load', self.xCloak);
	$w.addEventListener('resize', self.xCloak);

})();
</x-script>

</div>
