# wpkickstart

Version: `2.0.0`

This is a flexible WordPress Plugin framework I like to use. It uses namespacing, a class-based structure, a simple `app()` callable for calling different services, and Grunt for language management.

_Note this is intended for plugins, but can really go anywhere once it's converted._

Want to see how it works, see below and [/services/example-service](/services/example-service) and even [services/replace-cli](/services/replace-cli) for examples on what it can do.

## To Use

- Clone down in the `plugins/` directory
- Activate the plugin
- Run `wp kickstart help` to find out how to set it up for your use
- Once `wp kickstart` is done you will have a scaffolded plugin boilerplate ready to build on
- Start coding your plugin

_Note, the `wp kickstart` step is required as it gets rid of cruft you would otherwise have to do yourself._

### [app()](/app/) function...
### What are [Services](/services/)?
### What are [Components](/components/)?
### [Generating POT files](/languages/)...

_______________

# Changelog

## 2.0.0

This release introduces a new concept of "features" which are folders that contain the entire component of a feature. This allows you to more easily move features, their JS, CSS, etc as a whole to other projects, drop them in the `features/` folder, attach them and use them without having to figure out where files go.

- New [Services](/services/) and [Components](/components/) architecture
- `wp` CLI replacements, see `wp kickstart help` when the plugin is active

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
