<?php

namespace Btn\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Btn\MediaBundle\Model\MediaInterface;

/**
 * @ORM\Table(name="btn_media_video_filter", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="unq_media_filter",columns={"media_id", "filter"})
 * })
 * @ORM\Entity(repositoryClass="Btn\MediaBundle\Repository\MediaVideoFilterRepository")
 */
class MediaVideoFilter
{
    const WAITING = 'WAITING';
    const IN_PROGRESS = 'IN_PROGRESS';
    const COMPLETE = 'COMPLETE';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Btn\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $media;

    /**
     * @ORM\Column(name="filter", type="string")
     */
    protected $filter;

    /**
     * @ORM\Column(name="status", type="string")
     */
    protected $status;

    /**
     * MediaVideoFilter constructor.
     *
     * @param $media
     * @param $filter
     */
    public function __construct(MediaInterface $media, $filter)
    {
        $this->media = $media;
        $this->filter = $filter;
        $this->status = self::WAITING;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMedia()
    {
        return $this->media;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function inProgress()
    {
        $this->status = self::IN_PROGRESS;
    }

    public function done()
    {
        $this->status = self::COMPLETE;
    }

    public function isComplete()
    {
        return $this->getStatus() === self::COMPLETE;
    }
}
