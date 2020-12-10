<?php

namespace ABetter\Core;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider {

    public function boot() {

		Blade::directive('abettercore', function(){
			return "<?php echo 'ABETTER CORE!'; ?>";
		});

		// Shortcuts
		Blade::directive('notempty', function($expression){
			return "<?php if(!empty($expression)): ?>";
		});
		Blade::directive('ifnotempty', function($expression){
			return "<?php if(!empty($expression)): ?>";
		});
		Blade::directive('ifx', function($expression){
			return "<?php if(!empty($expression)): ?>";
		});
		Blade::directive('endnotempty', function($expression){
			return '<?php endif; ?>';
		});
		Blade::directive('endifnotempty', function($expression){
			return '<?php endif; ?>';
		});
		Blade::directive('endifx', function($expression){
			return '<?php endif; ?>';
		});

    }

    public function register() {
        //
    }

}
