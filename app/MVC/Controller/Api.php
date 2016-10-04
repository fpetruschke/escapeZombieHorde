<?php
/**
 * Created by Florian Petruschke
 * Date: 04.10.16
 * Time: 10:55
 */

namespace escapeZombieHorde\Controller;


use escapeZombieHorde\Model\Player;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Api {

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

        session_start();
        $_SESSION['player'] = $player;

        return new Response("Player created.", 200);
    }

    public function getSessionPlayer(Application $app) {
        session_start();

        if ($_SESSION['player'] == null) {
            return new Response("No active player in SESSION.", 500);
        } else {
            $sessionToJson = str_replace('*', '',str_replace('\\u0000', '', json_encode((array)$_SESSION['player'])));
            return new Response($sessionToJson, 200);
        }

    }

    public function deleteSessionPlayer(Application $app) {
        session_start();

        if ($_SESSION['player'] == null) {
            return new Response("No active player in SESSION.", 200);
        } else {
            session_unset();
            session_destroy();
            return new Response("Player deleted.", 200);
        }
    }

    public function startGame(Application $app) {
        session_start();

        if ($_SESSION['player'] == null) {
            return new Response("No active player in SESSION.", 200);
        } else {

            var_dump($_SESSION['player']->getStartLat());

            return $app['twig']->render(
                'game.html.twig',
                array(
                    'lat' => $_SESSION['player']->getStartLat(),
                    'long' => $_SESSION['player']->getStartLong()
                )
            );

        }
    }

}