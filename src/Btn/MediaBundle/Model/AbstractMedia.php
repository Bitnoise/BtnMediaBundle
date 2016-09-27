<?php

namespace Btn\MediaBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 */
abstract class AbstractMedia extends AbstractFile implements MediaInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="original_name", type="string", length=255, nullable=true)
     */
    protected $originalName;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    protected $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @var integer
     */
    protected $category;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255)
     * @Assert\NotBlank(groups={"fileMissing"})
     */
    protected $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    protected $size;

    /**
     * @var array
     */
    protected $thumbExtensions = array('jpeg', 'jpg', 'png', 'gif');

    /**
     * @var array
     */
    protected $videoExtensions = array('mp4');

    /**
     *
     */
    protected $previewIcons = array(
        'application/pdf' => 'pdf.png',
        'application/zip' => 'zip.png',
        'video/mp4' => 'mp4.png',
        '_default' => '_blank.png',
    );

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set originalName
     *
     * @param  string $originalName
     * @return Media
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Media
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * Set file
     *
     * @param  string         $file
     * @return RestaurantFile
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     *
     */
    public function isThumbable()
    {
        $extension = $this->getFileExt();
        if (($extension && in_array(strtolower($extension), $this->thumbExtensions))) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    public function isVideo()
    {
        $extension = $this->getFileExt();
        if (($extension && in_array(strtolower($extension), $this->videoExtensions))) {
            return true;
        }

        return false;
    }

    /**
     *
     */
    public function isIconable()
    {
        return $this->getIconPath() ? true : false;
    }

    /**
     *
     */
    public function getIconPath()
    {
        $type = $this->getType();
        if ($type && !empty($this->previewIcons[$type])) {
            return $this->previewIcons[$type];
        }

        if (!empty($this->previewIcons['_default'])) {
            return $this->previewIcons['_default'];
        }

        return false;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Media
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param  string $type
     * @return Media
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category
     *
     * @param  string $category
     * @return Media
     */
    public function setCategory(MediaCategoryInterface $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get category name
     */
    public function getCategoryName()
    {
        return $this->category ? $this->category->getName() : null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeFile()
    {
        if ($file = $this->getMediaPath()) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    /**
     *
     */
    public function getPath()
    {
        return $this->file;
    }

    /**
     *
     */
    public function setThumbExtensions(array $thumbExtensions)
    {
        $this->thumbExtensions = $thumbExtensions;
    }

    /**
     *
     */
    public function getThumbExtensions()
    {
        return $this->thumbExtensions;
    }

    /**
     *
     */
    public function isFileInEntity()
    {
        return $this->getFile() ? true : false;
    }

    /**
     * @return string
     */
    public function getChoiceLabel() {
        $choiceLabel = '';
        $name = $this->getName();
        $alt = $this->getAlt();

        $choiceLabel .= $name;

        if ($alt && $alt !== $name) {
            $choiceLabel .= ' (' .  $alt . ')';
        }

        return $choiceLabel;
    }

    /**
     *
     */
    public function __toString()
    {
        return $this->getName();
    }
}
