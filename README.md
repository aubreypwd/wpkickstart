# WP Plugin Boilerplate

This is a WordPress Plugin framework I like to use. It uses namespacing,
a class-based structure, a simple `app()` callable for calling different
parts of your plugin, and Grunt for language management.

## To Use

Simply clone it down and delete the `.git` stuff to start off. Open up
each file and start globally replacing things like `Plugin Name`,
`plugin-name`, `YourCompanyName`, `YourPluginName`, etc to start setting
it up for your needs. Just make sure you examine all the base files
and customize it for your needs.

The easiest way to create a new class is to duplicate the `class-shared.php`
file and clear out it's contents. Then attach it in the `App::attach()` method,
and if you're hooking into WordPress, create and call your new class' `hooks` method
in the `App::hooks()` method and start writing new code!

### `app()`

In any class you can always call the base `App` class using the `app()` function, e.g.

`app()->shared->method_in_shared_class()`

or...

`app()->my_new_class->method_in_my_new_class()`

### Language

I use Grunt to generate .pot files for language translations, simply run:

`grunt languages`

...which will generate a `plugin-name.pot` file in `languages/`. You can then
use PoEdit and tools like it to open the `.pot` file and generate language `.mo` files.
