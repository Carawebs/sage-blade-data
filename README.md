Data Controllers for Sage 9 Blade Templates
===========================================
Blade is [the simple, yet powerful templating engine provided with Laravel][1].

Using Blade within Laravel is simple - but using it within a WordPress project requires some extra work to make your data available in the view.

The Roots Sage theme uses Blade, and this plugin allows you to select which resources have access to data. This may be useful if the same data needs to be presented in different views. 

## Usage
Run:
```bash
composer require carawebs/sage-blade-data
```

## Theme Setup
To load, include the following at the theme level:
```php
<?php
namespace App;

use \Carawebs\SageBladeData\Loader;
use \Carawebs\SageBladeData\Filters;
// Optionally, see https://github.com/Carawebs/wp-metadata-accessor 
use \Carawebs\DataAccessor\PostMetaData;

/**
* Load Controllers
*/
add_action('wp', function() {
    new Loader(new Filters, new PostMetaData);
});
```

You could create a new file to contain this - remember to add such a file to the `array_map()` function in `functions.php`.

Create a directory in your theme (e.g. `/Carawebs/Controllers`). This will be your controller namespace.

Note that by default the path to the controllers directory is `get_template_directory() . '/Carawebs/Controllers'`. This is filtered by 'carawebs-controllers/path-to-controllers'.

Reference this in the theme `composer.json` to enable autoloading. For example:

```js
// composer.json
"autoload": {
    "psr-4": {
        "Roots\\Sage\\": "src/lib/Sage/",
        "Carawebs\\Controllers\\": "src/Carawebs/Controllers/"
    }
}
```
Then run:

```BASH
composer dump-autoload
```
...to regenerate Composer's autoload files.

## Usage
Controller classes must extend `Carawebs\SageBladeData\Controller`.

Controller classes must contain at least two methods:

- `targetTemplates()` returns an array of templates that should receive the data
- `dataToReturn()` retuyrn an array of data that will be made available in the specified templates

Here's an example:

```php
namespace Carawebs\Controllers;

use Carawebs\SageBladeData\Controller;
/**
 *
 */
class FrontPage extends Controller
{
    // Data available on 'home' and 'page' templates:
    public function targetTemplates()
    {
        return ['home', 'page'];
    }

    public function dataToReturn()
    {
        $carouselSubfields = [
            'image' => ['image_ID', 'full'],
            'description'=>'text'
        ];
        return [
            // Post metadata from https://github.com/Carawebs/wp-metadata-accessor
            // You can just access using ACF functions or similar.
            'pageIntro' => $this->postMeta->getField('metafield'),
            'intro' => $this->postMeta->getField('intro'),
            'carouselData' => $this->postMeta->getRepeaterField('slider', $carouselSubfields),
        ];
    }
}
```
The parent class optionally receives an object that controls post metadata, represented by `$this->postMeta`. You can ignore this if you like and build your own data-fetchers, but this is a good way of separating concerns.

## Accessing Data in Blade Templates
Because multiple controllers can add data to a single blade view, data variables are prefixed by the class name and camel cased.

For example, if you define:
```php
class About extends Controller
{
    public function targetTemplates()
    {
        return ['about'];
    }
    public function dataToReturn()
    {
        return [
            'intro' => "Hello World",
        ];
    }
}
```
https://laravel.com/docs/7.x/blade...you would access the defined variable in blade as `$aboutIntro`.

[1]: https://laravel.com/docs/7.x/blade
