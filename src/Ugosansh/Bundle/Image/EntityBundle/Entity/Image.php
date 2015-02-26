<?php

namespace Ugosansh\Bundle\Image\EntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Ugosansh\Component\Image\ImageInterface;

/**
 * Image
 *
 * @ORM\Table(name="image", uniqueConstraints={@ORM\UniqueConstraint(name="unique_slug", columns={"source", "slug"})})
 * @ORM\Entity(repositoryClass="Ugosansh\Bundle\Image\EntityBundle\Repository\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image implements ImageInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=32)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="mimeType", type="string", length=64)
     */
    private $mimeType;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=16)
     */
    private $extension;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(name="metadata", type="array", nullable=true)
     */
    private $metadata;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime")
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_update", type="datetime", nullable=true)
     */
    private $dateUpdate;

    /**
     * @var Image
     *
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="childs", cascade={"persist"})
     * @ORM\JoinColumn(name="id_parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="parent", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="id", referencedColumnName="id_parent")
     */
    private $childs;

    /**
     * @var string
     */
    private $binarySource;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childs = new ArrayCollection();
    }

    public function clear()
    {
        $this->id           = null;
        $this->path         = 'default';
        $this->binarySource = '';
    }

    /**
     * prePersist
     *
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->dateCreate = new \DateTime();
    }

    /**
     * preUpdate
     *
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->dateUpdate = new \DateTime();
    }

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
     * Set source
     *
     * @param string $source
     * @return Image
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Image
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Image
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return Image
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
     * Set extension
     *
     * @param string $extension
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string 
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Image
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set metadata
     *
     * @param array $metadata
     *
     * @return ImageInterface
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * Get metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Add metadata
     *
     * @param string $name
     * @param string $value
     *
     * @return ImageInterface
     */
    public function addMetadata($name, $value)
    {
        $this->metadata[$name] = $value;
    }

    /**
     * Remove metadata
     *
     * @param string $name
     *
     * @return ImageInterface
     */
    public function removeMetadata($name)
    {
        if (array_key_exists($name, $this->metadata)) {
            unset($this->metadata[$name]);
        }

        return $this;
    }

    /**
     * Set parent
     *
     * @param ImageInterface $parent
     * @return Image
     */
    public function setParent(ImageInterface $parent = null)
    {
        $parent->addChild($this);

        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return ImageInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add child
     *
     * @param ImageInterface $child
     * @return Image
     */
    public function addChild(ImageInterface $child)
    {
        $this->childs[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param ImageInterface $child
     */
    public function removeChild(ImageInterface $child)
    {
        $child->setParent(null);

        $this->childs->removeElement($child);
    }

    /**
     * Get child
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set binarySource
     *
     * @param string $binarySource
     *
     * @return Image
     */
    public function setBinarySource($binarySource)
    {
        $this->binarySource = $binarySource;

        return $this;
    }

    /**
     * Get binarySource
     *
     * @return string
     */
    public function getBinarySource()
    {
        return $this->binarySource;
    }


    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Image
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set dateUpdate
     *
     * @param \DateTime $dateUpdate
     * @return Image
     */
    public function setDateUpdate($dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    /**
     * Get dateUpdate
     *
     * @return \DateTime 
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

}
