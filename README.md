OPcache Dashboard
=================
Set up properly and monitor your Zend OPcache with this dashboard that will help you checking memory, hits and status, configuring for optimal performance (warning you when cache full, validation, etc.) and reseting one or all scripts with one click.

Installation
============

Composer installation:
```
composer require carlosio/opcache-dashboard
```
Then you can symlink it to your public folder or require it from another php file.

Or just copy and paste ```opcache.php``` anywhere in your public folder. You can use something such as:
```wget https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/opcache.php```

**Try to keep it safe for non authorized users.**

Development
===========

Run `docker-compose up` in project root directory. OPcache dashboard is available at http://localhost:8000/opcache.php

Start coding :)

The docker container configuration can be altered at build time using the following environment variables :

- `OPCACHE_DASHBOARD_PHP_VERSION` : PHP version to be installed in the container. Supported versions are listed on [PHP's Docker image page](https://store.docker.com/images/php) (`*-apache` images are supported)

Screenshots
===========
![Main page](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-1.png)
![Status](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-2.png)
![Configuration](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-3.png)
![Scripts](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-4.png)
