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

        if(!file_exists("/var/www/html/" . $app['androidApp'])) {
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
    public function showDocumentation(Application $app) {

        return $app['twig']->render(
            'documentation.html.twig',
            array()
        );

    }

    public function createNewPlayer(Application $app, Request $request) {

        if(!empty($request->query->get("lat")) && null != $request->query->get("lat")) {
            $latitude = $request->query->get("lat");
        } else {
            return new Response("Latitude is missing.", 500);
        }

        if(!empty($request->query->get("long")) && null != $request->query->get("long")) {
            $longitude = $request->query->get("long");
        } else {
            return new Response("Longitude is missing.", 500);
        }

        $player = new Player($latitude, $longitude);
        var_dump($player->getId());

        return new Response("Good.", 200);

    }

}
