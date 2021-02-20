<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'get'
        ]
    ],
    itemOperations: [
        'get' => ['method' => 'get']
    ],
    attributes: [
        'security' => 'is_granted("ROLE_USER")',
        'pagination_items_per_page' => 10,
        'pagination_maximum_items_per_page' => 30,
    ],
    cacheHeaders: ['public' => true],
    formats: ['json', 'jsonld', 'jsonhal'],
    normalizationContext: [
        'groups' => 'product:read',
        'swagger_definition_name' => 'Read',
    ]
)]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'price' => 'ASC'], arguments: ['orderParameterName' => 'order'])]
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Groups("product:read")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups("product:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups("product:read")
     */
    private $color;

    /**
     * @ORM\Column(type="smallint")
     * @Groups("product:read")
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
