<?php

namespace App\Security;

use App\Entity\Participant as AppParticipant;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
class ActifChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $participant): void
    {
        if (!$participant instanceof AppParticipant){
            return;
        }

        if (!$participant->isActif()){
            throw new CustomUserMessageAccountStatusException('Votre compte à été désactivé.');
        }
    }

    public function checkPostAuth(UserInterface $participant): void
    {
        if (!$participant instanceof AppParticipant){
            return;
        }

        if (!$participant->isActif()){
            throw new CustomUserMessageAccountStatusException('Votre compte à été désactivé.');
        }
    }
}