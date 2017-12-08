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

Authentication
==============

It is possible to restrict access to the dashboard using a request token.
To do so define the `OPCACHE_DASHBOARD_TOKEN` environment variable.

Further requests to the dashboard will require the `token` query parameter value to match the environment variable value.

Apache configuration example :

```
SetEnv OPCACHE_DASHBOARD_TOKEN my_secret_token
```

This configuration will restrict dashboard access unless the token parameter is correctly set : `opcache.php?token=my_secret_token`

Screenshots
===========
![Main page](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-1.png)
![Status](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-2.png)
![Configuration](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-3.png)
![Scripts](https://raw.github.com/carlosbuenosvinos/opcache-dashboard/master/thumbnail-4.png)
