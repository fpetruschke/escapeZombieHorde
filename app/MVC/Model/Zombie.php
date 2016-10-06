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
     * @return int
     */
    public function getHp()
    {
        return $this->hp;
    }

    /**
     * @param int
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