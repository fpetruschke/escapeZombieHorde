<?php
/**
 * Created by Florian Petruschke
 * Date: 04.10.16
 * Time: 10:55
 */

namespace escapeZombieHorde\Controller;


use escapeZombieHorde\Model\Inventory;
use escapeZombieHorde\Model\Player;
use escapeZombieHorde\Model\Zombie;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class Api {

    /**
     * createNewPlayer
     *
     * Creates a new player with the given latitude and longitude
     * and stores it into database.
     *
     * @param Application $app  Silex Application
     * @param Request $request  HTTP Request
     * @return Response         String and status code
     */
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
        if(!empty($request->query->get("name")) && null != $request->query->get("name")) {
            $name = $request->query->get("name");
        } else {
            return new Response("Username is missing.", 500);
        }

        // get entity Manager
        $em = $app['eM'];
        // starting transcation
        $em->getConnection()->beginTransaction();
        try {
            // creating users' inventory
            $newInventory = new Inventory();
            $newInventory->setAmmo(5);
            $newInventory->setKnife(true);
            $newInventory->setPistol(false);
            $newInventory->setSlots(5);
            $em->persist($newInventory);
            $em->flush();

            // creating the player
            $newPlayer = new Player();
            $newPlayer->setName($name);
            $newPlayer->setHp(100);
            $newPlayer->setStartLat(str_pad($latitude, 15, 0));
            $newPlayer->setStartLong(str_pad($longitude, 15, 0));
            $newPlayer->setCurrentLat(str_pad($latitude, 15, 0));
            $newPlayer->setCurrentLong(str_pad($longitude, 15, 0));

            $inventory = $em->find('escapeZombieHorde\Model\Inventory', $newInventory);
            $newPlayer->setInventory($inventory);
            $em->persist($newPlayer);
            $em->flush();
            $em->getConnection()->commit();

            // creating zombies - maximal amount of google map markers is 10
            $number = 10;
            for($i = 0; $i<$number; $i++) {
                $zombie = new Zombie();
                $zombie->setHp(rand(1,5));
                $zombie->setAmmo(rand(0,10));

                $lat = $this->generatePositionCloseToPlayer($latitude);
                $long = $this->generatePositionCloseToPlayer($longitude);

                $zombie->setCurrentLat($lat);
                $zombie->setCurrentLong($long);

                $em->persist($zombie);
                $em->flush();
            }

            session_start();
            $_SESSION['player'] = $newPlayer->getId();

            // success
            return new Response("Player and zombies created.", 200);

        } catch(\Exception $e) {
            // failure
            return new Response("Player could not be created: " . $e->getMessage(), 500);
        }


    }

    /**
     * generatePositionCloseToPlayer
     *
     * Is used for generating zombies around the player.
     * Method will either add or substract from players' longitude/latitude
     * for the zombies' longitude/latitude
     *
     * @param $playersLongOrLat float   latitude or longitude of the player
     * @return float
     */
    public function generatePositionCloseToPlayer($playersLongOrLat) {

        $indicator = rand(0,1);
        if(1 == $indicator) {
            $result = $playersLongOrLat + $this->getRandomFloat();
        } else {
            $result = $playersLongOrLat - $this->getRandomFloat();
        }
        return $result;

    }

    /**
     * getRandomFloat
     *
     * Method is used for generating zombies spawn positions near
     * the players position
     *
     * @return float
     */
    public function getRandomFloat() {
        $Min = 0.0008;
        $Max = 0.0020;
        $round=0;
        if ($Min>$Max) {
            $min=$Max;
            $max=$Min;
        } else {
            $min=$Min;
            $max=$Max;
        }
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if($round>0) {
            $randomfloat = round($randomfloat,$round);
        }
        return $randomfloat;
    }

    /**
     * getSessionPlayer
     *
     * Searches for the stored ($_SESSION) player id in the database
     * and returns the player as json object
     *
     * @param Application $app
     * @return Response
     */
    public function getSessionPlayer(Application $app) {
        session_start();

        if (!key_exists('player',$_SESSION) or $_SESSION['player'] == null) {
            return new Response("No active player.", 500);
        } else {

            // get entity Manager
            $em = $app['eM'];

            $player = $em->getRepository('escapeZombieHorde\Model\Player')->findOneBy(array("id" => $_SESSION['player']));
            if(empty($player)) {
                // failure: no active player
                return new Response("No active player in sesson.", 200);
            } else {
                // converting object to json
                $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
                $json = $serializer->serialize($player, 'json');
                return new Response($json, 200);
            }
        }

    }

    /**
     * getActivePlayers
     *
     * Searches all players from the db and returns them
     * as json objects
     *
     * @param Application $app
     * @return Response
     */
    public function getActivePlayers(Application $app) {
        // get entity Manager
        $em = $app['eM'];

        $players = $em->getRepository('escapeZombieHorde\Model\Player')->findAll();
        if(!empty($players)) {
            // converting object to json
            $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
            $json = $serializer->serialize($players, 'json');
            return new Response($json, 200);
        } else {
            return new Response("No active players.", 200);
        }
    }

    /**
     * deleteSessionPlayer
     *
     * Method searches for the player id stored in the $_SESSION inside the database
     * and deletes the player and the belonging inventory
     *
     * @param Application $app
     * @return Response
     */
    public function deleteSessionPlayer(Application $app) {
        session_start();

        if (!key_exists('player',$_SESSION) or $_SESSION['player'] == null) {
            return new Response("No active player in session.", 200);
        } else {

            // get entity Manager
            $em = $app['eM'];

            $player = $em->getRepository('escapeZombieHorde\Model\Player')->findOneBy(array("id" => $_SESSION['player']));
            if(empty($player)) {
                // failure: no active player
                return new Response("No active player in sesson.", 500);
            } else {
                $playerInventory = $player->getInventory();
                $playerInventoryId = $playerInventory->getId();

                $em->remove($player);
                $em->flush();

                $inventory = $em->getRepository('escapeZombieHorde\Model\Inventory')->findOneBy(array("id" => $playerInventoryId));
                $em->remove($inventory);
                $em->flush();

                session_unset();
                session_destroy();
                return new Response("Player deleted.", 200);
            }
        }
    }

    /**
     * deleteAllZombies
     *
     * Method deletes all zombies from database
     *
     * @param Application $app
     * @return Response
     */
    public function deleteAllZombies(Application $app) {
        // get entity Manager
        $em = $app['eM'];

        $zombies = $em->getRepository('escapeZombieHorde\Model\Zombie')->findAll();
        foreach ($zombies as $zombie) {
            $em->remove($zombie);
            $em->flush();
        }

        return new Response("Zombies deleted.", 200);
    }

    /**
     * deleteAll
     *
     * Method deletes all players, belonging inventories and zombies
     * from the database
     *
     * @param Application $app
     * @return Response
     */
    public function deleteAll(Application $app) {

        // get entity Manager
        $em = $app['eM'];

        // delete ALL players and their belonging inventories
        $players = $em->getRepository('escapeZombieHorde\Model\Player')->findAll();
        if(!empty($players)) {
            foreach ($players as $player) {
                $playerInventory = $player->getInventory();
                $playerInventoryId = $playerInventory->getId();

                $em->remove($player);
                $em->flush();

                $inventory = $em->getRepository('escapeZombieHorde\Model\Inventory')->findOneBy(array("id" => $playerInventoryId));
                $em->remove($inventory);
                $em->flush();
            }
        }

        // delete all zombies
        $zombies = $em->getRepository('escapeZombieHorde\Model\Zombie')->findAll();
        if(!empty($zombies)) {
            foreach ($zombies as $zombie) {
                $em->remove($zombie);
                $em->flush();
            }
        }

        session_start();
        session_unset();
        session_destroy();
        return new Response("Players, inventories and zombies deleted.", 200);

    }

    /**
     * startGame
     *
     * Gets the current players and zombies and displays them on the map
     *
     * @param Application $app
     * @return Response
     */
    public function startGame(Application $app) {
        session_start();

        if (!key_exists('player',$_SESSION) or $_SESSION['player'] == null) {
            return new Response("No active player in SESSION.", 200);
        } else {

            // get entity Manager
            $em = $app['eM'];
            // get player
            $player = $em->getRepository('escapeZombieHorde\Model\Player')->findOneBy(array("id" => $_SESSION['player']));
            if(empty($player)) {
                // failure: no active player
                return new Response("No active player in sesson.", 500);
            } else {

                $playerIcon = $app['serverRoot'] . "img/playerIcon.png";
                $zombieIcon = $app['serverRoot'] . "img/zombieIcon.png";

                $zombies = $em->getRepository('escapeZombieHorde\Model\Zombie')->findAll();
                $players = $em->getRepository('escapeZombieHorde\Model\Player')->findAll();

                return $app['twig']->render(
                    'game.html.twig',
                    array(
                        'player'    => $player,
                        'players'   => $players,
                        'zombies'   => $zombies,
                        'playerIcon'=> $playerIcon,
                        'zombieIcon'=> $zombieIcon,
                    )
                );
            }
        }
    }

    /**
     * updatePlayerPosition
     *
     * Method for updateing the stored players' positions (latitude and longitude)
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function updatePlayerPosition(Application $app, Request $request) {

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

        // check if player id is available in $_SESSION
        session_start();
        if (!key_exists('player',$_SESSION) or $_SESSION['player'] == null) {
            return new Response("No active player in session.", 500);
        } else {
            // get entity Manager
            $em = $app['eM'];
            // get player
            $player = $em->getRepository('escapeZombieHorde\Model\Player')->findOneBy(array("id" => $_SESSION['player']));
            if (empty($player)) {
                // failure: no player found
                return new Response("No active player in sesson.", 500);
            } else {
                $player->setCurrentLat($latitude);
                $player->setCurrentLong($longitude);
                $em->persist($player);
                $em->flush();
            }
            return new Response("Updated players' position", 200);
        }
    }

    public function updateZombiePosition(Application $app, Request $request) {

        if(!empty($request->query->get("id")) && null != $request->query->get("id")) {
            $zId = $request->query->get("id");
        } else {
            return new Response("Zombies' Id is missing.", 500);
        }
        if(!empty($request->query->get("lat")) && null != $request->query->get("lat")) {
            $zLat = $request->query->get("lat");
        } else {
            return new Response("Zombies' Latitude is missing.", 500);
        }
        if(!empty($request->query->get("long")) && null != $request->query->get("long")) {
            $zLong = $request->query->get("long");
        } else {
            return new Response("Zombies' Longitude is missing.", 500);
        }

        // get entity Manager
        $em = $app['eM'];
        // get player
        $zombie = $em->getRepository('escapeZombieHorde\Model\Zombie')->findOneBy(array("id" => $zId));
        if (empty($zombie)) {
            // failure: no player found
            return new Response("No zombie found with given Id.", 500);
        } else {
            $zombie->setCurrentLat($zLat);
            $zombie->setCurrentLong($zLong);
            $em->persist($zombie);
            $em->flush();
        }
        return new Response("Updated zombies' position", 200);

    }
}