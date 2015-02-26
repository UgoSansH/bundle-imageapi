<?php

namespace Ugosansh\Bundle\Image\ApiBundle\Manager;

use Ugosansh\Component\Image\ImageInterface;
use Ugosansh\Component\Image\ImageManagerInterface;
use Ugosansh\Component\Image\Resizer;
use Ugosansh\Component\Image\FileSystem;
use Ugosansh\Component\Image\Upload\Uploader;
use Doctrine\ORM\EntityManager;

/**
 * Manager
 */
class ImageManager implements ImageManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Resizer
     */
    protected $resizer;

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $repositoryName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * __construct
     *
     * @param EntityManager $entityManager  Doctrine entity manager
     * @param string        $repositoryName Repository name
     */
    public function __construct(EntityManager $entityManager, $repositoryName)
    {
        $this->entityManager  = $entityManager;
        $this->repositoryName = $repositoryName;
        $this->resizer        = null;
        $this->rootDir        = '';
        $this->entityName     = '';
    }

    /**
     * createEntity
     *
     * @return mixed
     */
    public function createEntity()
    {
        $className  = $this->getRepository()->getClassname();
        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new Exception(sprintf('Entity "%s" is not instantiable', $className));
        }

        $entity = $reflection->newInstance();
        $entity->setPath('default');

        return $entity;
    }

    /**
     * getRepository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->repositoryName);
    }

    /**
     * save
     *
     * @param mixed   $image Entity
     * @param boolean $flush  Flush image
     *
     * @return boolean
     */
    public function save(ImageInterface $image)
    {
        $this->uploadSource($image);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return true;
    }

    /**
     * remove
     *
     * @param mixed $image
     *
     * @return boolean
     */
    public function remove(ImageInterface $image)
    {
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return true;
    }

    /**
     * find
     *
     * @param integer $id Id entity
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * find unique image by criterias
     *
     * @param array   $criterias
     * @param mixed   $offset    null|integer
     * @param integer $limit     default 100
     *
     * @return array
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find image by size
     *
     * @param integer $id      Id image or parent
     * @param string  $source
     * @param integer $width
     * @param integer $height
     * @param integer $crop
     *
     * @return ImageInterface
     */ 
    public function findBySize($id, $source, $width, $height, $crop = null)
    {
        if (!$image = $this->findOneBy(['id' => $id, 'source' => $source])) {
            return null;
        }

        if ($image->getWidth() != $width || $image->getHeight() != $height) {
            $source = $this->fileSystem->getAbsolutePath($image->getPath());
            $this->resizer->setSource($source);

            if (!is_null($crop)) {
                $this->resizer->setRatio($crop);
            }

            $size = $this->resizer->defineSize($width, $height, $crop);

            if (!$child = $this->getRepository()->findBySize($id, $size['width'], $size['height'])) {
                // Resize
                if (!$child = $this->createChild($image, $width, $height, $crop)) {
                    throw new \Exception(sprintf('Failed to create child image of "%s"', $image->getId()));
                }

                return $child;
            }

            return $child;
        }

        return $image;
    }

    /**
     * Create image child
     *
     * @param ImageInterface $parent
     * @param integer        $width
     * @param integer        $height
     * @param integer        $crop
     *
     * @return ImageInterface
     */
    protected function createChild(ImageInterface $parent, $width, $height, $crop = null)
    {
        $source = $this->fileSystem->getAbsolutePath($parent->getPath());
        $image  = $this->fileSystem->hydrateImageInfo($this->createEntity(), $source);

        $image->setParent($parent);
        $image->setTitle($parent->getTitle());
        $image->setSource($parent->getSource());
        $image->setPath($this->fileSystem->defineImagePath($image));

        $destination    = $this->fileSystem->getAbsolutePath($image->getPath());
        $destinationDir = substr($image->getPath(), 0, strrpos($image->getPath(), '/'));

        $this->fileSystem->createDirectory($destinationDir);
        $this->resizer->setSource($source);

        if (!is_null($crop)) {
            $this->resizer->setRatio($crop);
        }

        if ($this->resizer->resize($destination, $width, $height)) {
            $image = $this->fileSystem->hydrateImageInfo($image, $destination);
            $this->save($image);

            return $image;
        }

        return false;
    }

    /**
     * Upload image source
     *
     * @param ImageInterface $image
     *
     * @return ImageInterface
     */
    protected function uploadSource(ImageInterface $image)
    {
        if ($binary = $image->getBinarySource()) {
            $image = $this->uploader->uploadBase64($image, $binary);
        }

        return $image;
    }

    public function getImageSource(ImageInterface $image)
    {
        return $this->fileSystem->getImageSource($image);
    }

    /**
     * Set uploader
     *
     * @param Uploader $uploader
     *
     * @return FormHandler
     */
    public function setUploader(Uploader $uploader)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * setResizer
     *
     * @param Resizer $resizer
     *
     * @return ImageManager
     */
    public function setResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;

        return $this;
    }

    /**
     * Set fileSystem
     *
     * @param FileSystem $fileSystem
     *
     * @return Uploader
     */
    public function setFileSystem(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;

        return $this;
    }

}