<?php

namespace ABetter\Core;

use Illuminate\View\Component;

class CloakComponent extends Component {

	public $view = 'abetter::components.cloak.cloak';

	// ---

    public function render() {
		return function(array $data) {
			return view($this->view)->with([
				'data' => $data,
				'slot' => $data['slot'],
				'attributes' => $data['attributes'],
			])->render();
    	};
    }

}
