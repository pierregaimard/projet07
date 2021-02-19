<?php

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\FilterExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class UserCollectionDataProvider implements ContextAwareCollectionDataProviderInterface,
    RestrictedDataProviderInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private PaginationExtension $pagination,
        private FilterExtension $filter
    ){}

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return  User::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): Paginator
    {
        # Set custom queryBuilder
        $customer = $this->security->getUser()->getCustomer();
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->andWhere('u.customer = :customer')
            ->setParameter('customer', $customer)
        ;

        $generator = new QueryNameGenerator();

        # Enable filters & pagination
        $this->filter->applyToCollection($queryBuilder, $generator, $resourceClass, $operationName, $context);
        $this->pagination->applyToCollection($queryBuilder, $generator, $resourceClass, $operationName, $context);

        return $this->pagination->getResult($queryBuilder, $resourceClass, $operationName, $context);
    }
}
