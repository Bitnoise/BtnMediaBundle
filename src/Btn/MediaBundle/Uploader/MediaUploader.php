<?php

namespace Btn\MediaBundle\Uploader;

use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Btn\MediaBundle\Adapter\AdapterInterface;
use Btn\BaseBundle\Helper\FileHelper;

class MediaUploader
{
    /** @var array $allowedExtensions */
    private $allowedExtensions;
    /** @var int $sizeLimit */
    private $sizeLimit;
    /** @var bool $replaceOldFiles */
    private $replaceOldFiles;
    /** @var \Gaufrette\Filesystem $filesystem */
    private $filesystem;
    /** @var array $errors */
    private $errors;
    /** @var array $uploadedFiles */
    private $uploadedFiles;
    /** @var array $uploadedMedias */
    private $uploadedMedias;
    /** @var string $cacheDirectory */
    private $cacheDirectory;
    /** @var Btn\MediaBundle\Adapter\AdapterInterface\ $adapter */
    private $adapter;
    /** @var boolean $autoExtract */
    private $autoExtract;

    /**
     *
     */
    public function __construct($cacheDirectory, $autoExtract = true)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->autoExtract = $autoExtract;

        $this->reset();
    }

    /**
     *
     */
    public function reset()
    {
        $this->allowedExtensions = array();
        $this->sizeLimit         = FileHelper::toBytes(ini_get('upload_max_filesize'));
        $this->filesystem        = null;
        $this->replaceOldFiles   = false;
        $this->file              = null;
        $this->errors            = array();
        $this->uploadedFiles     = array();
        $this->uploadedMedias    = array();
    }

    /**
     * @param $error
     *
     * @return MediaUploader
     */
    public function addError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @param array $allowedExtensions
     *
     * @return MediaUploader
     */
    public function setAllowedExtensions(array $allowedExtensions)
    {
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);

        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }

    /**
     * @param $sizeLimit
     *
     * @return MediaUploader
     */
    public function setSizeLimit($sizeLimit)
    {
        FileHelper::checkServerSizeLimit($sizeLimit);

        $this->sizeLimit = $sizeLimit;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeLimit()
    {
        return $this->sizeLimit;
    }

    /**
     * @param Filesystem $filesystem
     *
     * @return MediaUploader
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param bool $replaceOldFiles
     *
     * @return MediaUploader
     */
    public function setReplaceOldFiles($replaceOldFiles)
    {
        $this->replaceOldFiles = (bool) $replaceOldFiles;

        return $this;
    }

    /**
     *
     */
    public function getReplaceOldFiles()
    {
        return $this->replaceOldFiles;
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return !empty($this->uploadedFiles);
    }

    /**
     * @param UploadedFile $file
     *
     */
    private function saveUpload(UploadedFile $file = null)
    {
        $media = $this->adapter->getFormData();
        if (count($this->uploadedMedias) > 0) {
            if (!$media->getId()) {
                $media = clone $media;
                if ($media->getOriginalName() === $media->getName()) {
                    $media->setName(null);
                }
            } else {
                throw new \Exception('Multiupload only avalible with new media');
            }
        }
        if ($file) {
            $extension = $file->guessExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename  = $basename = preg_replace(
                array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'),
                array('_', '.', ''),
                $originalName
            );

            if (false == $this->getReplaceOldFiles()) {
                $counter = 0;
                if ($this->filesystem) {
                    while ($this->filesystem->has($filename.'.'.$extension)) {
                        $filename = $basename.$counter++;
                    }
                } else {
                    $directory = $media->getUploadRootDir();
                    while (file_exists($directory.DIRECTORY_SEPARATOR.$filename.'.'.$extension)) {
                        $filename = $basename.$counter++;
                    }
                }
            }

            $filename .= '.'.$extension;

            $media->setOriginalName($originalName);
            $media->setName($media->getName() ? $media->getName() : $originalName);
            $media->setFile($filename);
            $media->setType($file->getMimeType());

            if ($this->filesystem) {
                $gaufrette = $this->filesystem->get($filename, true);
                $gaufrette->setContent(file_get_contents($file->getRealPath()));
            } else {
                $file->move($media->getUploadRootDir(), $filename);
            }

            $this->uploadedFiles[] = $filename;
        }
        $this->uploadedMedias[] = $media;
    }

    /**
     * @param UploadedFile $file
     */
    private function handleZip(UploadedFile $file)
    {
        $zip = new \ZipArchive();
        if ($zip->open($file->getRealPath()) === true) {
            $cacheDirectory = $this->cacheDirectory.'/'.md5(time());
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);

                if (strpos($filename, '__MACOSX') !== false || strpos($filename, '.') === 0) {
                    continue;
                }

                if ($zip->extractTo($cacheDirectory, array($filename)) && is_file($cacheDirectory.'/'.$filename)) {
                    $file = new UploadedFile($cacheDirectory.'/'.$filename, basename($filename));
                    $this->handleFile($file);
                }
            }
            $this->deleteDirectory($cacheDirectory);
            $zip->close();
        } else {
            $this->addError('Could not open ZIP archive.');
        }
    }

    /**
     * @param UploadedFile $file
     *
     * @return MediaUploader
     */
    private function handleFile(UploadedFile $file)
    {
        if ($file == null) {
            return $this->addError('No files were uploaded.');
        }

        if ($file->getSize() == 0) {
            return $this->addError('File is empty.');
        }

        if ($file->getSize() > $this->sizeLimit) {
            return $this->addError('File is too large.');
        }

        $extension = $file->guessExtension();

        if (empty($extension)) {
            return $this->addError('File has no extension.');
        }

        if ($this->allowedExtensions && !in_array($extension, $this->allowedExtensions)) {
            return $this->addError(
                'File has an invalid extension, it should be one of '.implode(', ', $this->allowedExtensions).'.'
            );
        }

        $this->saveUpload($file);
    }

    /**
     * @param string $path
     */
    private function deleteDirectory($path)
    {
        if (is_file($path)) {
            @unlink($path);
        } else {
            array_map(array(__CLASS__, __FUNCTION__), glob($path.'/*'));
        }

        @rmdir($path);
    }

    /**
     * @param UploadedFile $file
     *
     * @return MediaUploader
     */
    public function handleUpload(UploadedFile $file = null)
    {
        if (null === $file) {
            if (!$this->adapter) {
                throw new \Exception(sprintf('Adapter is missing in "%s". set it via setAdapter()', __CLASS__));
            }
            $file = $this->adapter->getUploadedFile();
        }

        if ($file) {
            if ($this->autoExtract && 'zip' === $file->guessExtension()) {
                $this->handleZip($file);
            } else {
                $this->saveUpload($file);
            }
        }

        return $this;
    }

    /**
     * Set adapter and handleUpload
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @return array
     */
    public function getUploadedMedias()
    {
        return $this->uploadedMedias;
    }
}
