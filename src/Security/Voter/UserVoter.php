<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CAN_MANAGE_USER = 'user.canManageUser';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (in_array($attribute, [
                self::CAN_MANAGE_USER,
            ])) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::CAN_MANAGE_USER => $this->canManageUser($user),
            default => false,
        };
    }

    public function canManageUser(UserInterface $user): bool
    {
        return !empty(array_intersect($user->getRoles(), [User::ROLE_ADMIN]));
    }
}
