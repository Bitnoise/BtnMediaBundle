<?php

namespace Btn\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Btn\MediaBundle\Model\AbstractMediaCategory;

/**
 * @ORM\Table(name="btn_media_category")
 * @ORM\Entity(repositoryClass="Btn\MediaBundle\Repository\MediaCategoryRepository")
 */
class MediaCategory extends AbstractMediaCategory
{
    /**
     * @ORM\OneToMany(targetEntity="Btn\MediaBundle\Entity\Media", mappedBy="category")
     */
    protected $files;
}
