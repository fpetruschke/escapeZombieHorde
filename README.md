# escapeZombieHorde

**An android app that interacts with a webservice tracking your footsteps and spawning zombies near you.**

**Between the 01.10.2016 and the 31.10.2016 you can visit `http://zombie.menkar.uberspace.de`**

This application contains a webservice (running on an apache2 webserver - connected to a mysql database) and a client (android app).  
The client calls the apis' routes with predefined parameters.   

The web application is based on PHP framework Silex 1.3 ( [http://silex.sensiolabs.org/documentation](http://silex.sensiolabs.org/documentation) ).


------

# Content

* [Requirements](#requirements)
* [Dependencies](#dependencies)
* [Installation](#installation)
* [Testing](#testing)
* [API](#api)

------

## Requirements

* Apache WebServer >=2.4.7
* PHP >=5.4
* MySQL Server >=5.5.47 
* Composer >= 1.0.0-alpha10

## Dependencies

```
    "silex/silex": "~1.3",
    "twig/twig": ">=1.8,<2.0-dev",
    "symfony/yaml":"v2.2.0",
    "symfony/config":"v2.2.0",
    "doctrine/orm": "^2.5",
    "monolog/monolog": "^1.17",
    "igorw/config-service-provider": "^1.2",
    "symfony/serializer": "^3.1"
``` 

The project uses jQuery(v1.12.0) and Bootstrap(v3.3.6).

------

## Installation

**`Please read the installation instructions - else you might not be able to locally run the project...`**

### Git Clone
First of all you need to clone or download this repository.  
Make sure you have the permissions to read, write and execute.  
**NOTE: Please do not change the project's root folder name. It must be "escapeZombieHorde"**  

### Composer update
Now you need to run a composer install:

`composer install` and `composer update`

#### .htaccess
We want to address this little project with typing **`localhost/escapeZombieHorde/`**.  
Since the index.php is located under the web-directory we don't want to have to expand the url with /web.  
Therefore we need to configure an `.htaccess`-file inside the root of the project.  
Put following content into it:    

```
// .htaccess

<IfModule mod_rewrite.c>
    Options -MultiViews
    Options +SymLinksIfOwnerMatch
    RewriteEngine On
    RewriteCond %{DOCUMENT_ROOT}/web/$1 -f
    RewriteRule ^((.+)/?)*$ /web/$1 [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ web/index.php [QSA,L]
    RewriteRule ^\.htaccess$ - [F]
</IfModule>

```

#### rewrite.mod
Check if apaches' rewrite module is enabled: 

`ll /etc/apache2/mods-enabled/ | grep 'rewrite.load'` 

(If not (nothing shows up) run `sudo a2enmod rewrite` and restart your apache.)


### environment variable

Since the application searches for your environment and differs between "productive" and "development",  
you have to set a "dev" environment variable in your apache config.  
How to do this depends on your setup.  

In most cases, this will do it:  

`sudo nano /etc/apache/sites-available/000-default.conf`

add following line to the config file:

```

SetEnv APP_ENV dev


```

Save, close and run:

`sudo service apache reload && sudo service apache restart`


### web/index.php  

**Here you have to check the roots: **

```php

// web/index.php

/**
 * defining the projects root
 */
$app['serverRoot']  = /*Root for all asset stuff (css, img, js)      */;
$app['urlRoot']     = /*Root for your callable urls (ajax-requests): */;

```

### DATABASE

You can use the provided script under "app/config/database/" for creating the database.  
Please adjust the "app/config/environment/" files to your configurations.  

# Testing

## PHPUnit

**`NOTE: this is still a WIP - right now there is only one "placeholder"-test...`**

PHPUnit-Tests are unser `tests/MVC/`.

Execute tests: **`phpunit --bootstrap vendor/autoload.php tests/MVC/`**

...with testdox: **`phpunit --bootstrap vendor/autoload.php --testdox tests/MVC/`**

...with coverage(HTML): **`phpunit --bootstrap vendor/autoload.php --coverage-html tests/Coverage tests/MVC/`**

Coverage-directory: `tests/Coverage`

------

#  API

For documentation please go to `localhost/escapeZombieHorde/documentation`  


------