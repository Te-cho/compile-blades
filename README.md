small change
# compile-blades 

A Laravel package for compiling blades nested in 1 file into 1 flattened file.

 
## Why?

> For best performance, you may want to consider flattening your blades on production, cause a lot of nesting consumes time in laravel
> since each nested level repeats the same pipline process, that consumes time & memory.

Example of problems:
- https://stackoverflow.com/questions/30673129/laravel-blades-performance/44863712#44863712
- https://laracasts.com/discuss/channels/laravel/how-to-improve-laravel-views-performance-when-using-multiple-times-same-view-file-or-howto-avoid-repeating-expensive-read-file-operation


**Table of Contents**

- [Installation](#installation)
    - [1) Require the package](#2-require-the-package)
    - [2) Configure Laravel](#3-configure-laravel)
- [Usage](#usage)
    - [Generating flatened blade file](#generating-flattened-blade)
- [A demo](#a-demo)
    - [Perfomance Insights results](#profiler-resutls)


### 1) Require the package

Next, you'll need to require the package using Composer:

From your project's base path, run:

    $ composer require te-cho/compile-blades

### 2) Configure Laravel

#### Service Provider

Add the following to the `providers` key in `config/app.php`:

``` php
'providers' => [
    Techo\CompileBlades\CompileBladesServiceProvider::class,
];
```

#### Console

To get access to the `compile:blades` command, add the following to the `$commands` property in `app/Console/Kernel.php`:

``` php
protected $commands = [
    \Techo\CompileBlades\Console\CompileBlades::class,
];
```

## Usage

Before getting started, I highly recommend reading through Laravels documentation on Views and Blades.

### Flattening Views:

Providing everything is set up and configured properly, all you need to do in order to flatten a view for a certain route or something else, is running the following command:

    $ php artisan compile:blades view-name

This will generate a flattened view instead of the current one.


## Example: 
Lets say we have a view called test.blade.php that is called by one of our controllers, which is including another view
inside of it, but the problem is that its looping in it, which causes the include to happen alot which cause performance drops.
So we run the following command:

    $ php artisan compile:blades test

### Input File
![test.blade.php](https://goo.gl/hwNSCc)
![subviews/included-test.blade.php](https://goo.gl/jkoseH)

### Output File
![test.blade.php](https://goo.gl/PGRkJk)
