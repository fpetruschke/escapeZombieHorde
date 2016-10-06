<?php
/**
 * Created by Florian Petruschke
 * Date: 05.10.16
 * Time: 21:11
 */

namespace escapeZombieHorde\Model;


class ActivePlayers
{

    public $activePlayers = array();

    public function addPlayer(Player $player) {
        //array_push($this->activePlayers, $player);
        $this->activePlayers[$player->getId()] = $player;
    }

    public function removePlayer(Player $player) {
        if(($key = array_search($player, $this->activePlayers, true)) !== FALSE) {
            unset($this->activePlayers[$key]);
        }
    }

    public function getPlayers() {
        return $this->activePlayers;
    }

}