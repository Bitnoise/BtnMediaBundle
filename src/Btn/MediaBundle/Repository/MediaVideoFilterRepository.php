<?php

namespace Btn\MediaBundle\Repository;

use Btn\MediaBundle\Entity\MediaVideoFilter;
use Btn\MediaBundle\Model\MediaInterface;
use Doctrine\ORM\EntityRepository;

class MediaVideoFilterRepository extends EntityRepository
{
    public function findAllToEncode()
    {
        $qb = $this->createQueryBuilder('mvf');

        $qb->andWhere('mvf.status = :status');
        $qb->setParameter('status', MediaVideoFilter::WAITING);

        $q = $qb->getQuery();

        return $q->getResult();
    }

    public function findOneByMediaAndFilterName(MediaInterface $media, $filterName)
    {
        $qb = $this->createQueryBuilder('mvf');

        $qb->andWhere('mvf.media = :media');
        $qb->andWhere('mvf.filter = :filter');

        $qb->setParameter('media', $media);
        $qb->setParameter('filter', $filterName);

        $q = $qb->getQuery();

        return $q->getOneOrNullResult();
    }
}
