<?php

namespace Alx\TestTaskBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Attachment
 */
class Attachment implements \JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $order;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $size;

    /**
     * @var \Alx\TestTaskBundle\Entity\Document
     */
    private $document;

    /**
     * @var UploadedFile
     */
    private $file;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return Attachment
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Attachment
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
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return Attachment
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Attachment
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set document
     *
     * @param \Alx\TestTaskBundle\Entity\Document $document
     *
     * @return Attachment
     */
    public function setDocument(\Alx\TestTaskBundle\Entity\Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \Alx\TestTaskBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     *
     * @return Attachment
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        $this->setName($file->getClientOriginalName());
        $this->setMimeType($file->getClientMimeType());
        $this->setSize($file->getClientSize());

        return $this;
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function uploadFile()
    {
        $this->getFile()->move(
            $this->getUploadPath(),
            $this->getId()
        );
    }

    public function removeFile()
    {
        unlink($this->getFilePath());
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'size' => $this->getSize()
        ];
    }

    public function getFilePath()
    {
        return $this->getUploadPath() . $this->getId();
    }

    private function getUploadPath()
    {
        return __DIR__ . '/../../../../uploads/';
    }
}
