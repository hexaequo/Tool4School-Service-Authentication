<?php


namespace App\Tests\integration;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class IntegrationTestCase extends KernelTestCase
{
    private EntityManagerInterface|null $entityManager = null;
    private UserPasswordEncoderInterface|null $passwordEncoder = null;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->entityManager = self::$container->get('doctrine.orm.entity_manager');

        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    public function getEntityManager() {
        return $this->entityManager;
    }

    public function getUserPasswordEncoder() {
        if(!$this->passwordEncoder) {
            $this->passwordEncoder = self::$container->get('security.password_encoder');
        }
        return $this->passwordEncoder;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
