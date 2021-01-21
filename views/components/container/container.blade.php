@php

$Xcontainer = (object) $data ?? NULL;
$Xcontainer->slot = $slot ?? "";
$Xcontainer->class = "";
$Xcontainer->attr = "";
foreach ($Xcontainer->attributes??[] AS $key => $val) {
	if ($key == 'class') {
		$Xcontainer->class .= " {$val}";
	} else if (in_array($key,['pointer','progress'])) {
		$Xcontainer->attr .= " {$key}";
	} else if ($val) {
		$Xcontainer->attr .= " $key=\"{$val}\"";
	}
}

@endphp

<div class="component--x-container {{ $Xcontainer->class }}" {{ $Xcontainer->attr }} x-container>
	<div class="--x-contained" x-contained>
		{{ $Xcontainer->slot }}
	</div>

<x-script>
(function(){

	var self = this,
		$w = window,
		$d = document,
		$b = $d.documentElement;

	$w.xContainer = function(e) {

	    var els = $d.querySelectorAll('[x-container]'); // IE breaks with '--';
		[].forEach.call(els,function(el){

	        var rect = el.getBoundingClientRect(),
				cs = el.style,
				cl = el.classList,
				rb = rect.bottom,
				rw = rect.width,
				rh = rect.height,
				ww = $w.innerWidth,
				wh = $w.innerHeight,
				con,
				sp;

			cs.setProperty('--w', Math.round(rw) );
			cs.setProperty('--h', Math.round(rh) );

			cs.setProperty('--vw', ((rw / ww) * 100).toFixed(2) + '%' );
			cs.setProperty('--vh', ((rh / wh) * 100).toFixed(2) + '%' );

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

			// ---

			if (el.hasAttribute('progress')) {

				sp = Math.round(((rh + wh) - rb) / (rh + wh) * 100) / 100;
				if (sp > 1) { sp = 1; } else if (sp < 0) { sp = 0; };

				cs.setProperty('--progress', sp);
				cs.setProperty('--dir', self.dir);

			};

			// ---

			if (el.hasAttribute('pointer')) {

				con = el.querySelector('[x-contained]');
				con.onmousemove = self.xMpos;

			};

	    });
	};

	// ---

	self.xMpos = function(e) {

		if (typeof e.target.closest !== "function") return;

		var el = e.target.closest('[x-container]'),
			cs = el.style,
			ew = el.offsetWidth,
			eh = el.offsetHeight,
			ex = e.pageX,
			ey = e.pageY,
			px = ex - el.offsetLeft,
			py = ey - el.offsetTop;

		cs.setProperty('--px', ((px / ew) * 100).toFixed(2) + '%' );
		cs.setProperty('--py', ((py / eh) * 100).toFixed(2) + '%' );

	};

	// ---

	self.xCall = function(el,cb) {
		var func = (el.getAttribute(cb)||'').replace(/^\"([^\"\(]+).*/,'$1'); if (!func) return;
		if (func && (eval('typeof('+func+') == typeof(Function)'))) {
			return $w[func](el,cb);
		};
	};

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
	$w.addEventListener('scroll', $w.xContainer);
	$w.addEventListener('load', $w.xContainer);
	$w.addEventListener('resize', $w.xContainer);

})();
</x-script>

<x-style>
.component--x-container {
	//display: block;
	//position: relative;
	width: 100%;
	height: 100%;
	.\--x-contained {
		//display: block;
		//position: relative;
		width: 100%;
		height: 100%;
	}
}
</x-style>

</div>
