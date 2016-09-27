<?php

namespace Btn\MediaBundle\Model;

interface MediaInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set originalName
     *
     * @param  string $originalName
     * @return Media
     */
    public function setOriginalName($originalName);

    /**
     * Get originalName
     *
     * @return string
     */
    public function getOriginalName();

    /**
     * Set name
     *
     * @param  string $name
     * @return Media
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();


    /**
     * @return string
     */
    public function getAlt();

    /**
     * @param string $alt
     */
    public function setAlt($alt);
    /**
     * Set file
     *
     * @param  string         $file
     * @return RestaurantFile
     */
    public function setFile($file);

    /**
     * Get file
     *
     * @return string
     */
    public function getFile();

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size = null);

    /**
     *
     */
    public function isThumbable();

    /**
     *
     */
    public function isVideo();

    /**
     *
     */
    public function isIconable();

    /**
     *
     */
    public function getIconPath();

    /**
     * Set description
     *
     * @param  string $description
     * @return Media
     */
    public function setDescription($description);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set type
     *
     * @param  string $type
     * @return Media
     */
    public function setType($type);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();
    /**
     * Set category
     *
     * @param  string $category
     * @return Media
     */
    public function setCategory(MediaCategoryInterface $category = null);

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory();
    /**
     * Get category name
     */
    public function getCategoryName();

    public function removeFile();

    /**
     *
     */
    public function getPath();

    /**
     * @param array $thumbExtensions
     */
    public function setThumbExtensions(array $thumbExtensions);

    /**
     * @return array
     */
    public function getThumbExtensions();

    /**
     *
     */
    public function isFileInEntity();
}
