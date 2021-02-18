<?php


namespace App\MessageHandler\Authentication;


use App\Entity\User;
use App\Exception\Authentication\BadCredentialsException;
use App\Exception\Authentication\UserNotFoundException;
use App\MessageHandler\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GetJWTHandler extends AbstractHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $passwordEncoder,
        private JWTEncoderInterface $JWTEncoder
    )
    {
    }

    public function __invoke(array $data)
    {
        $this->checkFieldsMissing($data,['username','password']);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username'=>$data['username']]);

        if(!$user) throw new UserNotFoundException($data['username']);

        if(!$this->passwordEncoder->isPasswordValid($user,$data['password'])) throw new BadCredentialsException();

        return [
            'code' => 200,
            'token' => $this->JWTEncoder->encode(['username'=>$data['username']])
        ];
    }
}
