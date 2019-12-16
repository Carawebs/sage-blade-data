<?php
namespace Carawebs\SageBladeData;

/**
 * Filter class 
 */
class Filters
{
    /**
     * Hook data to WordPress 'template_include' filter.
     * Note how Sage amends paths in `/srv/www/example.com/current/web/app/themes/sage/resources/functions.php` 
     * @return void
     */
    public function init()
    {
	    add_filter('template_include', function ($template) {
            $potentialHooks = get_body_class();
            $data = [];
            foreach ($potentialHooks as $templateClass) {
                $datum = apply_filters("carawebs/template/{$templateClass}/data", NULL, $template);
                if(empty($datum)) continue;
                $data[] = $datum;
            }

	    // First run `array_filter` with no params to remove empty elements. Then run `array_merge(...)`
	    // on the result to simplify the array.
            if (!empty($data)) {
                $data = array_merge(...array_filter($data));
	    }
            echo \App\template($template, $data);
            return get_stylesheet_directory() . '/index.php';
        });
    }
}
