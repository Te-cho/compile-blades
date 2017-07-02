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
    - [3) Configure Laravel](#3-configure-laravel)
- [Usage](#usage)
    - [Generating flatened blade file](#generating-flattened-blade)
- [A demo](#a-demo)
    - [Perfomance Insights results](#profiler-resutls)


.. to be continued soon..
