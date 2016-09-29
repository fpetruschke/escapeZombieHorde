<?php
/**
 * Created by Florian Petruschke
 * Date: 29.09.16
 * Time: 21:27
 */

namespace escapeZombieHorde\Model;


class Zombie
{

    /**
     * @var     int     Id of the zombie
     */
    protected $id;

    /**
     * @var     int     Number of health points
     */
    protected $hp;

    /**
     * @var     int     Amount of ammo
     */
    protected $ammo;

    /**
     * @var     float   Starting latitude
     */
    protected $startingLat;

    /**
     * @var     float   Starting longitude
     */
    protected $startingLong;

    /**
     * @var     float   Current latitude
     */
    protected $currentLat;

    /**
     * @var     float   current longitude
     */
    protected $currentLong;

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

    /**
     * @return float
     */
    public function getStartingLat()
    {
        return $this->startingLat;
    }

    /**
     * @param float $startingLat
     */
    public function setStartingLat($startingLat)
    {
        $this->startingLat = $startingLat;
    }

    /**
     * @return float
     */
    public function getStartingLong()
    {
        return $this->startingLong;
    }

    /**
     * @param float $startingLong
     */
    public function setStartingLong($startingLong)
    {
        $this->startingLong = $startingLong;
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
    
}