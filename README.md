# Laravel Modular Architecture

This project implements a **custom modular architecture for Laravel** that allows features to be separated into independent modules.
Each module contains its own controllers, models, routes, views, middleware, services, migrations, and configuration.

The goal is to make the project **scalable, maintainable, and organized** similar to package-based development.

---

# Module Structure

All modules live inside the `Modules` directory.

Example:

```
Modules/
 └ Blog
    ├ Controllers
    │   └ PostController.php
    ├ Models
    │   └ Post.php
    ├ Services
    │   └ PostService.php
    ├ Middleware
    │   └ PostMiddleware.php
    ├ Providers
    │   └ ModuleServiceProvider.php
    ├ routes
    │   └ web.php
    ├ views
    │   └ post.blade.php
    ├ config
    │   └ config.php
    ├ migrations
    └ module.json
```

Each module acts like a **mini Laravel application**.

---

# Features

The modular system provides the following features:

* Automatic module discovery
* Automatic Service Provider registration
* Route prefix per module
* Module-specific middleware registration
* Module configuration loading
* Module migrations
* Module view namespaces
* Service layer generation
* CLI module generator

---

# Module Generator Command

Modules can be created using the custom artisan command:

```
php artisan module:make-all {ModuleName} {EntityName}
```

Example:

```
php artisan module:make-all Blog Post
```

This will generate a complete module with:

* Controller
* Model
* Service class
* Middleware
* View
* Routes
* Config file
* Service provider
* Module metadata

---

# Auto Route Prefix

Each module automatically gets its own route prefix.

Example module:

```
Blog
```

Routes inside the module will automatically become:

```
/blog
/blog/posts
/blog/{id}
```

Prefix is defined in:

```
Modules/Blog/config/config.php
```

Example:

```
return [
    'route_prefix' => 'blog'
];
```

---

# Using Views

Views inside modules are namespaced.

Example file:

```
Modules/Blog/views/post.blade.php
```

Access in controller:

```
return view('blog.post');
```

Format:

```
view('module.view')
```

---

# Service Layer

Each module includes a **Service class** for business logic.

Example:

```
Modules/Blog/Services/PostService.php
```

Example usage inside controller:

```
use Modules\Blog\Services\PostService;

class PostController extends Controller
{
    protected $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $posts = $this->service->all();
        return view('blog.post', compact('posts'));
    }
}
```

This keeps controllers **thin and maintainable**.

---

# Middleware Per Module

Each module can contain its own middleware.

Example:

```
Modules/Blog/Middleware/PostMiddleware.php
```

Middleware is automatically registered and can be used in routes:

```
Route::middleware(['postmiddleware'])->group(function () {
    Route::get('/', [PostController::class, 'index']);
});
```

---

# Module Routes

Routes are stored inside:

```
Modules/{Module}/routes/web.php
```

Example:

```
Route::get('/', [PostController::class, 'index']);
```

The system automatically applies:

* `web` middleware
* module route prefix

---

# Module Loader

Modules are automatically scanned and registered during application boot.

This allows you to simply drop a module inside the `Modules` folder and it will be loaded automatically.

---

# Composer Autoload

Make sure the `Modules` namespace is registered in `composer.json`.

```
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Modules\\": "Modules/"
    }
}
```

Then run:

```
composer dump-autoload
```

---

# Advantages of This Architecture

* Clear separation of features
* Scalable for large applications
* Easy to maintain
* Encourages service layer architecture
* Prevents controller bloat
* Modular development similar to packages

---

# Example Workflow

Create a new module:

```
php artisan module:make-all Shop Product
```

This generates a ready-to-use module:

```
Modules/Shop
```

Routes automatically available at:

```
/shop
```

Controller:

```
Modules/Shop/Controllers/ProductController.php
```

View:

```
Modules/Shop/views/product.blade.php
```

---

# Future Improvements

Possible enhancements:

* API route support
* Module assets
* Module translations
* Module commands
* Module policies
* Module testing support

---

# Summary

This project uses a **custom Laravel modular architecture** designed to keep large applications organized and maintainable.

Modules behave like small Laravel packages and can be developed independently while still integrating seamlessly with the main application.
