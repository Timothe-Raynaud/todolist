<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const CAN_DELETE_TASK = 'task.canDeleteTask';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::CAN_DELETE_TASK
            ])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::CAN_DELETE_TASK => $this->canDeleteTask($subject, $user),
            default => false,
        };

    }

    public function canDeleteTask(Task $task, UserInterface $user): bool
    {
        $taskUser = $task->getUser();
        if (!$taskUser instanceof User){
            return false;
        }

        return $taskUser->getUsername() === 'anonyme' ?
            !empty(array_intersect($user->getRoles(), [User::ROLE_ADMIN])) :
            $taskUser === $user;
    }
}
