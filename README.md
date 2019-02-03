# wpkickstart

This is a flexible WordPress Plugin framework I like to use. It uses namespacing, a class-based structure, a simple `app()` callable for calling different services, and Grunt for language management.

Want to see how it works, see below and [/services/example-service](/services/example-service) and even [services/replace-cli](/services/replace-cli) for examples on what it can do.

Built and tested for PHP 5.6.20 through 7.2.0

_________________

[Installation &rarr;](https://github.com/aubreypwd/wpkickstart/wiki/Installation)

[Get more information on the Wiki &rarr;](https://github.com/aubreypwd/wpkickstart/wiki)

_______________

# Changelog

## 2.0.0

This release introduces a new concept that introduces mobility of things you build. It separates out services (think of these as features) and components (think of these as reusable things services use to do things) into `services/` and `components/`. 

The idea is that a service or a component (really just a folder of files) can have it's own classes, Javascripts, CSS, etc and each can move to other wpkickstart frameworks by moving their folder into the other one with minor code changes for it to work.

This should make the things you build in wpkickstart easily re-usable in other projects built on wpkickstart.

- New [Services](/services/) and [Components](/components/) architecture ([note component namespace changes](/components/))
- `wp` CLI replacements, see `wp kickstart build` after you activate wpkickstart to set it up for your specific project
- Easier `wp` based install, see [Installation](https://github.com/aubreypwd/wpkickstart/wiki/Installation)
- More class flexibility (now if you `new Anything()` it will automatically autoload `class-anything.php` no matter where you put it)

### Backwards Compatibility

`2.0.0` is somewhat backwards compatible with later versions, but you may have to group your classes that used to be in `includes/` with a [component](https://github.com/aubreypwd/wpkickstart/wiki/Components) or a [service](https://github.com/aubreypwd/wpkickstart/wiki/Services).

______________________

### 1.2

This minor release fixes a major issue with `$app` being in the global space and conflicts with other plugins using the same architecture. #5

### 1.1

This is based off of some of 1.0's uses in different projects and has been improved to be more stable!

- Added `App::version()` and `App::header()` methods for easy access to version and header information
- Added Grunt for easy `.pot` file creation, etc
- Adds `App->wp-debug` for easy `WP_DEBUG` detection
- Uses `App::attach()` method to attach new classes (must add manually)
- Uses `App::hooks()` method to run hooks when WP is ready (must add manually)
- Adds better `app()` callback and global `$app` that can alternatively be called using `global $app`
- Hardened `phpunit` testing for a good starting point for how the framework should work, you should be able to add additional tests with ease

### 1.0

This came directly from a breakdown of the WDS SSO plugin that used my app framework. It does not work perfectly and has issues that could be problematic, so please use a future version.
