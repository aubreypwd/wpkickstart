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

**Also remember to rename the `plugin-name.php` file to be the same.**

The easiest way to create a new class is to duplicate the `class-shared.php`
file and clear out it's contents. Then attach it in the `App::attach()` method,
and if you're hooking into WordPress, create and call your new class' `hooks` method
in the `App::hooks()` method and start writing new code!

**Make sure you clear out this README for your own!**

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

_______________

# Changelog

## 1.2

This minor release fixes a major issue with `$app` being in the global space and conflicts with other plugins using the same architecture. #5

## 1.1

This is based off of some of 1.0's uses in different projects and has been improved to be more stable!

- Added `App::version()` and `App::header()` methods for easy access to version and header information
- Added Grunt for easy `.pot` file creation, etc
- Adds `App->wp-debug` for easy `WP_DEBUG` detection
- Uses `App::attach()` method to attach new classes (must add manually)
- Uses `App::hooks()` method to run hooks when WP is ready (must add manually)
- Adds better `app()` callback and global `$app` that can alternatively be called using `global $app`
- Hardened `phpunit` testing for a good starting point for how the framework should work, you should be able to add additional tests with ease

## 1.0

This came directly from a breakdown of the WDS SSO plugin that used my app framework. It does not work perfectly and has issues that could be problematic, so please use a future version.
