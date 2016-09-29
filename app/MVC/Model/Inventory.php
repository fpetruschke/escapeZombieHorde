<?php

namespace escapeZombieHorde\Model;

/**
 * Class Inventory
 *
 * Represents the inventory of the player
 *
 */
class Inventory
{

    /**
     * @var     int     Id of the inventory
     */
    protected $id;

    /**
     * @var     int     Id of the belonging player
     */
    protected $playerId;

    /**
     * @var     int     Number of slots inside the inventory
     */
    protected $slots;

    /**
     * @var     boolean     True if player has a knife
     */
    protected $knife;

    /**
     * @var     boolean     True if player has a pistol
     */
    protected $pistol;

    /**
     * @var     int         Amount of ammo for guns
     */
    protected $ammo;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param int $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * @return int
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * @param int $slots
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }

    /**
     * @return boolean
     */
    public function isKnife()
    {
        return $this->knife;
    }

    /**
     * @param boolean $knife
     */
    public function setKnife($knife)
    {
        $this->knife = $knife;
    }

    /**
     * @return boolean
     */
    public function isPistol()
    {
        return $this->pistol;
    }

    /**
     * @param boolean $pistol
     */
    public function setPistol($pistol)
    {
        $this->pistol = $pistol;
    }

    /**
     * @return int
     */
    public function getAmmo()
    {
        return $this->ammo;
    }

    /**
     * @param int $ammo
     */
    public function setAmmo($ammo)
    {
        $this->ammo = $ammo;
    }
    
}