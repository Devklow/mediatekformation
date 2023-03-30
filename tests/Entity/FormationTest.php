<?php

namespace App\Tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{

    public function testGetPublishedAtString()
    {
        $formation = new Formation();
        $formation->setPublishedAt(new \DateTime('2021-01-04 17:00:12'));
        $result = "04/01/2021";
        self::assertSame($result, $formation->getPublishedAtString());
    }
}
