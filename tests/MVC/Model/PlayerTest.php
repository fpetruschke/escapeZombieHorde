<?php
/**
 *
 */

namespace escapeZombieHorde\Model;
require_once __DIR__ . '/../../../app/MVC/Model/Player.php';


class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePlayer()
    {
        $testPlayer = new Player();
        $testPlayer->setHp(100);
        $this->assertEquals(100, $testPlayer->getHp());

    }
}
