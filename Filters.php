<?php
namespace Carawebs\SageBladeData;

/**
 *
 */
class Filters
{

    function __construct()
    {
        //$this->init();
    }

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

            // First run `array_filter` with no params to remove empty elements. Then
            // run `array_merge(...)` on the result to simplify the array.
            if (!empty($data)) {
                $data = array_merge(...array_filter($data));
            }
            echo \App\template($template, $data);

            return get_theme_file_path('index.php');
        }, PHP_INT_MAX);
    }
}
