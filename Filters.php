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
                $data[] = apply_filters("carawebs/template/{$templateClass}/data", NULL, $template);
            }

            // First run `array_filter` with no params to remove empty elements. Then
            // run `array_merge(...)` on the result to simplify the array.
            $data = array_merge(...array_filter($data));
            echo \App\template($template, $data);

            return get_theme_file_path('index.php');
        }, PHP_INT_MAX);

        /**
         * Tell WordPress how to find the compiled path of comments.blade.php
         */
        add_filter('comments_template', 'App\\template_path');
    }
}
