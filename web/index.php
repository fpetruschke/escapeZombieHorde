<?php
/**
 * index.php
 *
 * Configuration and setting up of the silex application.
 *
 * @author  Florian Petruschke <florian.petruschke@gmail.com>
 * @date    13.01.16  -  15:42
 * @version 1.0
 */

require_once '../vendor/autoload.php';
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Silex\Application;

// Instantiation of Silex Application
$app = new Application();

/**
 * Defining project roots and sources
 */
$app['serverRoot']  = "";
$app['urlRoot']     = "";
$app['androidApp']  = "";

/*
 * Registering service provider for twig template engine
 */
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../app/MVC/View', // The path to the templates, which is in our case points to /var/www/templates
    'twig.options' => array('debug' => false)
));
$app['twig']->addExtension(new Twig_Extension_Debug());
$app['twig']->addGlobal('current_page_name', $app['routes']);


/*
 * Registering routes from yaml config
 */
$app['routesConfigFile'] = '';
$app['routes'] = $app->extend('routes', function(RouteCollection $routes, Application $app) {
    $configDirectories = array(__DIR__ . '/../app/config/routes');
    $locator = new FileLocator($configDirectories);
    $loader = new YamlFileLoader($locator);
    $collection = $loader->load($app['routesConfigFile']);
    $routes->addCollection($collection);
    return $routes;
});

/*
 * Registering simple logController
 */
$app['log'] = new \escapeZombieHorde\Controller\Tools\logController(__DIR__ . '/../app/logs');

// for debugging purposes set to "true"
$app['debug'] = false;

/*
 * Registering a service provider for accessing active players
 */
$app["activePlayers"] = $app->share(function () {
    return new \escapeZombieHorde\Model\ActivePlayers();
});

/**
 * setting environment vars
 */
$env = getenv('APP_ENV') ?: 'prod';
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../app/config/environment/".$env.".yml"));

// run Silex application with the above configuration
$app->run();