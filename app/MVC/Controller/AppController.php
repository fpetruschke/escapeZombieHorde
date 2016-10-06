<?php

namespace escapeZombieHorde\Controller;
use escapeZombieHorde\Model\Player;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AppController
 *
 * Controls the basic requests
 *
 */
class AppController
{

    /**
     * showIndex
     *
     * Handles showing the index/mainpage
     *
     * @param Application $app  Silex Application
     * @return mixed            returns template
     */
    public function showIndex(Application $app) {

        return $app['twig']->render(
            'index.html.twig',
            array()
        );
    }

    /**
     * checkIfAndroidAppIsExistent
     *
     * Checks if the application is available/existent and returns
     * the result to the frontend for the user
     * 
     * @param Application $app  Silex Application
     * @return mixed            Json Object with the result
     */
    public function checkIfAndroidAppIsExistent(Application $app) {

        var_dump(__DIR__ . "/../../../.." .$app['androidApp']);

        if(!file_exists(__DIR__ . "/../../../.." .$app['androidApp'])) {
            return json_encode(array('response' => array(
                'message' => 'The android application is not available at the moment.',
                'code' => 'note'
            )));
        } else {
            return json_encode(array('response' => array(
                'message' => 'Please make sure you enabled "install applications from unknown sources" on your smartphone.',
                'code' => 'success'
            )));
        }
    }

    /**
     * showDocumentation
     *
     * @param Application $app
     * @return mixed
     */
    public function showDocumentation(Application $app)
    {

        return $app['twig']->render(
            'documentation.html.twig',
            array()
        );

    }

}
