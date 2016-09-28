<?php

namespace Btn\MediaBundle\Repository;

use Btn\MediaBundle\Entity\MediaVideoFilter;
use Btn\MediaBundle\Model\MediaInterface;
use Doctrine\ORM\EntityRepository;

class MediaVideoFilterRepository extends EntityRepository
{
    public function getVideosToEncodeQueryBuilder()
    {
        $qb = $this->createQueryBuilder('mvf');

        $qb->andWhere('mvf.status = :status');
        $qb->setParameter('status', MediaVideoFilter::WAITING);
        $qb->orderBy('mvf.id', 'DESC');

        return $qb;
    }

    public function findVideosToEncode($limit)
    {
        $qb = $this->getVideosToEncodeQueryBuilder();

        $q = $qb->getQuery();
        $q->setMaxResults($limit);

        return $q->getResult();
    }

    public function findAllToEncode()
    {
        $qb = $this->getVideosToEncodeQueryBuilder();
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
