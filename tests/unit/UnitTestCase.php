<?php


namespace App\Tests\unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class UnitTestCase extends KernelTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
