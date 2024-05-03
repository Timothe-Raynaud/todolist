<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const CAN_MANAGE_TASK = 'task.canManageTask';

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
                self::CAN_MANAGE_TASK
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
            self::CAN_MANAGE_TASK => $this->canManageTask($subject, $user),
            default => false,
        };
    }

    public function canManageTask(Task $task, UserInterface $user): bool
    {
        $taskUser = $task->getUser();
        if (!$taskUser instanceof User){
            return false;
        }

        return $taskUser->getUsername() === $this->parameterBag->get('anonyme_user') ?
            !empty(array_intersect($user->getRoles(), [User::ROLE_ADMIN])) :
            $taskUser === $user;
    }
}
