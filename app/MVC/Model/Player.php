<?php

namespace escapeZombieHorde\Model;

/**
 * User
 *
 * Represents the user
 *
 */
class Player
{

    /**
     * @var     int     Id of the player
     */
    protected $id;

    /**
     * @var     string  Playername / Username
     */
    protected $name;

    /**
     * @var     int     Healthpoints of the player
     */
    protected $hp;

    /**
     * @var     Inventory   Belonging inventory
     */
    protected $inventoryId;

    /**
     * @var     float   Longitude of the position the player started at
     */
    protected $startLong;

    /**
     * @var     float   Latitude of the position the player started at
     */
    protected $startLat;

    /**
     * @var     float   Current Longitude the player is at
     */
    protected $currentLong;

    /**
     * @var     float   Current Latitude the player is at
     */
    protected $currentLat;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getHp()
    {
        return $this->hp;
    }

    /**
     * @param int $hp
     */
    public function setHp($hp)
    {
        $this->hp = $hp;
    }

    /**
     * @return Inventory
     */
    public function getInventoryId()
    {
        return $this->inventoryId;
    }

    /**
     * @param Inventory $inventoryId
     */
    public function setInventoryId($inventoryId)
    {
        $this->inventoryId = $inventoryId;
    }

    /**
     * @return float
     */
    public function getStartLong()
    {
        return $this->startLong;
    }

    /**
     * @param float $startLong
     */
    public function setStartLong($startLong)
    {
        $this->startLong = $startLong;
    }

    /**
     * @return float
     */
    public function getStartLat()
    {
        return $this->startLat;
    }

    /**
     * @param float $startLat
     */
    public function setStartLat($startLat)
    {
        $this->startLat = $startLat;
    }

    /**
     * @return float
     */
    public function getCurrentLong()
    {
        return $this->currentLong;
    }

    /**
     * @param float $currentLong
     */
    public function setCurrentLong($currentLong)
    {
        $this->currentLong = $currentLong;
    }

    /**
     * @return float
     */
    public function getCurrentLat()
    {
        return $this->currentLat;
    }

    /**
     * @param float $currentLat
     */
    public function setCurrentLat($currentLat)
    {
        $this->currentLat = $currentLat;
    }
}
