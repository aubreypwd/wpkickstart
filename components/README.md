# Components

A component is essentially a package that is used by a [Services](/services/README.md). Components are small tools meant to be move/copied/installed from one wpkickstart framework to another.

In the [example service](/services/example-service) you can see it uses a component. Likewise in our own [replace-cli](/services/replace-cli) service you can see it uses a few components itself.

## How to load a component

Unlike [services](/services/README.md), components are meant to be loaded in any service. Let's say you have a service like:

```php
<?php

namespace YourCompanyName\YourPluginName\Service;

class My_Service {

    public $my_compnent;

    public function __construct() {
        $this->my_component = new My_Component();
    }

    public function hooks() {

    }

    public function run() {

    }
}
```

This service is auto loading e.g. `components/my-component/class-my-component.php` as a new instance. 

Your component may just be a single class, but since it's loaded from the `/my-components/` folder your component could have it's own `assets/`, etc that _it_ uses when you load it. It can also have it's own sub-classes if you want (but consider making those their own component?).

Now, even from another [service](/services/README.md) you can call this component via `app()->my_service->my_component`.

You can even (if you want to) attach components to `app()` via the same method we attach a [service](/services/README.md) to `app()` if you wanted to. Components are reusable tools meant to help services.

__________

The idea behind components are incredibly mobile groups of classes, javascript, css, etc _as a tool_ that can move around project to project. Components are grouped into folders because they, themselves, may also have their own `js` and `css` and dependencies that all move around with the component.
