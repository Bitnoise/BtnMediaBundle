<?php

namespace Btn\MediaBundle\Model;

abstract class AbstractFile
{
    private $fieldFile = 'file';
    private $fieldPath = 'path';

    /**
     *
     */
    public function getFile()
    {
        $method = 'get'.ucfirst($this->fieldFile);

        return $this->$method();
    }

    /**
     *
     */
    public function setFile($file)
    {
        $method = 'set'.ucfirst($this->fieldFile);

        return $this->$method($file);
    }

    /**
     *
     */
    public function getPath()
    {
        $method = 'get'.ucfirst($this->fieldPath);

        return $this->$method();
    }

    /**
     *
     */
    public function setPath($path)
    {
        $method = 'set'.ucfirst($this->fieldPath);

        return $this->$method($path);
    }

    /**
     *
     */
    public function getFileExt()
    {
        $file = $this->getFile();

        return $file ? strtolower(substr($file, strrpos($file, '.') + 1)) : $file;
    }

    /**
     *
     */
    public function getExt()
    {
        return $this->getFileExt();
    }
}
