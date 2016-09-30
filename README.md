[![Build Status](https://travis-ci.org/iulyanp/elixir-bundle.svg?branch=master)](https://travis-ci.org/iulyanp/elixir-bundle)

ElixirBundle
============

`ElixirBundle` is a [Symfony](http://symfony.com/) bundle that integrates [Laravel Elixir](https://github.com/laravel/elixir). 
The purpose of the bundle is to offer elixr() twig function.
This is exactly the same elixir() function from [Laravel](https://laravel.com) blade templating system.

### Requirements
Before you start installing this bundle you must first ensure that Node.js is installed on your machine.
Next, you'll want to install NPM and pull in Gulp as a global NPM package.

## Installation

### Step 1: Require the bundle with composer

Open your terminal and run one of the following commands to download the bundle into your vendor directory.

If you have composer installed globally you can run:
```
$ composer require iulyanp/elixir-bundle
```
Else you can go with:
```
$ php composer.phar require iulyanp/elixir-bundle
```

### Step 2: Register the bundle in your AppKernel class

Register the bundle in the app/AppKernel.php file of your project:

```
<?php
/** app/AppKernel.php */

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(

            new Iulyanp\ElixirBundle\IulyanpElixirBundle(),
        );
    }
}
```

### Step 3: Configure the bundle

In order for elixir to know where you keep your asset files you should configure the following:
```
iulyanp_elixir:
    assets_dir: 'app/Resources/assets'
    web_dir: '%kernel.root_dir%/../web'
    build_dir: 'build'
```

The `assets_dir` parameter is the relative path where you place your asset files.
The `web_dir` parameter is the relative path to where the generated assets will be stored.
** For example in our case the css files will be stored under `'%kernel.root_dir%/../web/css/'` folder.
The `build_dir` is the folder from within the web_dir where the asset version will be stored.
** For example in our case the css versioned assets will be stored in `"%kernel.root_dir%/../web/build/css/"` folder.

> Note! The `web_dir` parameter is the only *required* parameter. If are not set, the other two, will use the defaults.

### Step 4: Generate the package.json and gulpfile files:
If you already have installed `Node.js`, `npm` and `Gulp` you should be all set to run:

```
$ php bin/console elixir:init
```
A base `package.json` and a `gulpfile.js` will be generated into your project root directory.
Then run `npm install` to install all the dependencies and [laravel-elixir](https://github.com/laravel/elixir).

### Usage
Now you can use elixir() function to version a file like this:
```
<link rel="stylesheet" type="text/css" href="{{ elixir('css/app.css') }}" />
```

#### Bundle assets
By default the bundle assumes that you'll keep your assets files under the `app/Resources/public` folder.
In case that you have assets on bundles level (ex. src/AppBundle/Resources/public/sass/test.scss) you can compile them with elixir pretty simple.
For our example you'll have something like this:
```
// Set up Elixir tasks.
elixir(function(mix) {
    mix.sass('app.scss')
        .version(['web/css/app.css']);

    mix.sass(
        'test.scss', // the sass file you want to compile
        'web/css/test', // the path where you want the compiled css file to be saved
        'src/AppBundle/Resources/public/sass' // the path where your sass files are kept inside a bundle
    );
});
```
The `test.css` file will be saved to `web/css/test/test.css`.

### License
The ElixirBundle is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
