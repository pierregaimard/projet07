<?php

namespace App\Security\Voter;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

final class UserVoter extends Voter
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        $supportAttribute = in_array($attribute, ['USER_READ', 'USER_UPDATE', 'USER_DELETE']);
        $supportSubject = $subject instanceof User;

        return $supportAttribute && $supportSubject;
    }

    /**
     * @param string         $attribute
     * @param User           $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return false;
        }

        return match ($attribute) {
            'USER_READ', 'USER_UPDATE' =>
                $token->getUser()->getCustomer()->getId() === $subject->getCustomer()->getId(),
            'USER_DELETE' =>
                $token->getUser()->getCustomer()->getId() === $subject->getCustomer()->getId() &&
                $token->getUser()->getId() !== $subject->getId()
        };
    }
}
