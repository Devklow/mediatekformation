<?php

namespace App\Tests\Controller;

use App\Controller\AdminFormationController;
use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

class AdminFormationControllerTest extends TestCase
{

    public function testIsValidDate()
    {
        $formation = new Formation();
        $controller = new AdminFormationController();
        $formation->setPublishedAt(new \DateTime('2021-01-04 17:00:12'));
        self::assertSame(true, $controller->isValidDate($formation->getPublishedAt()));
        $formation->setPublishedAt(new \DateTime('2024-01-04 17:00:12'));
        self::assertSame(false, $controller->isValidDate($formation->getPublishedAt()));
        $formation->setPublishedAt(new \DateTime('now'));
        self::assertSame(true, $controller->isValidDate($formation->getPublishedAt()));
    }
}
