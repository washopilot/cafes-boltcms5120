<?php

namespace App;

use Bolt\Entity\Taxonomy;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TagCloudTwigExtension extends AbstractExtension
{
    private $objectManager;

    public function __construct(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tag_cloud_by_contenttype', [$this, 'tagCloudByContentType']),
        ];
    }

    public function tagCloudByContentType(string $contentType): array
    {
        $qb = $this->objectManager->createQueryBuilder();

        $qb->select('t.name, t.slug')
            ->addSelect('COUNT(c.id) as count')
            ->from(Taxonomy::class, 't')
            ->leftJoin('t.content', 'c')
            ->where('t.type = :taxonomyType')
            ->andWhere('c.contentType = :contentType')
            ->groupBy('t.name, t.slug')
            ->orderBy('t.name', 'ASC')
            ->setParameters([
                'taxonomyType' => 'tags',
                'contentType' => $contentType,
            ]);

        return $qb->getQuery()->getResult();
    }
}
