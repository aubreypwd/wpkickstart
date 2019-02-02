# Services

Services are essentially "conduct" operations and can be explained as a feature of your plugin.

Imagine you have a [component](/components/README.md) that you use over and over project to project. Your service might use that component to do something, or use many components to do something.

## Creating a Service

Just make a folder in `services/` like `my-service` and add a `class-my-service.php` that looks like:

```php
<?php

namespace YourCompanyName\YourPluginName\Service;

class My_Service {

    public function hooks() {

    }

    public function run() {

    }
}
```

Now go find [class-app.php](/app/class-app.php) and find `attach_services` method and add e.g. the following:

```php
$this->my_service = new Service\Example_Service();
```

This will attach the service to your `app()` and you can access it with `app()->my_service` from other services loaded this same way. All your services can talk to each other this way and it's not wrong to build dependencies between your services this way but it is suggested to keep dependencies to a minimum.
 
Note that your service's class file will be autoloaded and will now have it's `run` and `hooks` methods run automatically at the right time too.

A service is also a group of things, meaning a service folder might have a few classes, Javascript, and CSS that can move project to project if needed.
