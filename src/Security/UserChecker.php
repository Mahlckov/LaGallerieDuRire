<?php
namespace App\Security;

use App\Entity\Humouriste;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof Humouriste) {
            return;
        }
        if (!$user->isActif()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte a été désactivé, vous ne pouvez plus vous connecter!'
            );
        }
    }
    public function checkPostAuth(UserInterface $user)
    {
        $this->checkPreAuth($user);
    }
}