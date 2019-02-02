# Components

A component is essentially a package that is used by a [Services](/services/README.md). Components are small tools meant to be move/copied/installed from one wpkickstart framework to another.

In the [example service](/services/example-service) you can see it uses a component. Likewise in our own [replace-cli](/services/replace-cli) service you can see it uses a few components itself.

## How to load a component

Unlike [services](/services/README.md), components are meant to be loaded in any service. Let's say you have a service like:

```php
<?php

namespace YourCompanyName\YourProjectName\Service;

class My_Service {

    public $my_compnent;

    public function __construct() {
        $this->my_component = new \AnotherCompanyName\My_Component\My_Class();
    }

    public function hooks() {

    }

    public function run() {

    }
}
```

This service is auto loading e.g. `components/my-component/class-my-component.php` as a new instance, which may look like:

```php
<?php

namespace AnotherCompanyName\My_Component

class My_Class {

}
```

Notice the namespace is not `YourCompanyName\YourProjectName` but could be a component from another company or project. The namespace here should not be the same as your project, that way the component can move from project to project and be reused.

Your component may just be a single class, or it could be a group of classes, Javascript, CSS, etc. 

You can even call this component in another [service](/services/README.md) via `app()->my_service->my_component`.

The idea behind components are incredibly mobile groups of classes, javascript, css, etc _as a tool_ that can move around project to project. Components are grouped into folders because they, themselves, may also have their own `js` and `css` and dependencies that all move around with the component.
