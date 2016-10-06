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


    public function generatePositionCloseToPlayer($playersLongOrLat) {

        $indicator = rand(0,1);
        if(1 == $indicator) {
            $result = $playersLongOrLat + $this->getRandomFloat();
        } else {
            $result = $playersLongOrLat - $this->getRandomFloat();
        }
        return $result;

    }

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
                return new Response("No active player in sesson.", 500);
            } else {
                // converting object to json
                $serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
                $json = $serializer->serialize($player, 'json');
                return new Response($json, 200);
            }
        }

    }

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

    public function startGame(Application $app) {
        session_start();

        if (!key_exists('player',$_SESSION) or $_SESSION['player'] == null) {
            return new Response("No active player in SESSION.", 200);
        } else {

            // get entity Manager
            $em = $app['eM'];

            $player = $em->getRepository('escapeZombieHorde\Model\Player')->findOneBy(array("id" => $_SESSION['player']));
            if(empty($player)) {
                // failure: no active player
                return new Response("No active player in sesson.", 500);
            } else {

                $playerIcon = $app['serverRoot'] . "img/playerIcon.png";
                $zombieIcon = $app['serverRoot'] . "img/zombieIcon.png";

                // get entity Manager
                $em = $app['eM'];
                $zombies = $em->getRepository('escapeZombieHorde\Model\Zombie')->findAll();

                return $app['twig']->render(
                    'game.html.twig',
                    array(
                        'player'    => $player,
                        'zombies'   => $zombies,
                        'playerIcon'=> $playerIcon,
                        'zombieIcon'=> $zombieIcon,
                    )
                );
            }
        }
    }

}