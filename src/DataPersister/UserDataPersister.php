<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $passwordEncoder,
        private Security $security,
    ){}

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User  $data
     * @param array $context
     *
     * @return User
     */
    public function persist($data, array $context = []): User
    {
        # Set encoded password if needed
        if (null !== $data->getPlainPassword()) {
            $data->setPassword($this->passwordEncoder->encodePassword($data, $data->getPlainPassword()));
        }

        # Set user customer as same as logged user customer at creation time
        if (
            array_key_exists('collection_operation_name', $context) &&
            $context['collection_operation_name'] === 'post'
        ) {
            $data->setCustomer($this->security->getUser()->getCustomer());
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        $data->eraseCredentials();

        return $data;
    }

    /**
     * @param User  $data
     * @param array $context
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
