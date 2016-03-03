<?php

namespace Ugosansh\Bundle\Image\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Image entity controller
 */
class ImageController extends Controller
{
    /**
     * Get manager
     *
     * @return ImageManager
     */
    protected function getManager()
    {
        return $this->get('ugosansh_image_api.image.manager');
    }

    /**
     * Get source
     *
     * @return string
     */
    protected function getSource()
    {
        if ($user = $this->getUser()) {
            return $user->getUsername();
        }

        return $this->container->getParameter('ugosansh_image_api.default_source');
    }

    /**
     * Form handler image entity
     *
     * @param ImageInterface $image
     *
     * @return View
     */
    protected function createEditForm($image)
    {
        $handler = $this->get('ugosansh_image_api.form_handler');
        $form    = $this->get('form.factory')->createNamed('', $this->get('ugosansh_image_api.image.form_type'), $image);

        if (!$handler->process($form, $this->get('request'))) {
            return $this->handleView($this->view($handler->getErrors($form), 422));
        }

        if (!$this->getManager()->save($image)) {
            return $this->handleView($this->view('Failed to save image', 500));
        }

        return $this->forward('UgosanshImageApiBundle:Image:getInfoImage', ['id' => $image->getId()]);
    }

    /**
     * Get image info action
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image info",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getInfoImageAction($id)
    {

        $criteria = [
            'id'     => $id,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id), 404));
        }

        return $this->handleView($this->view($image, 200, ['Content-Type' => 'application/json'], ['Default', 'Detail']));
    }

    /**
     * Get image action
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getImageAction($slug)
    {
        $criteria = [
            'slug'   => $slug,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $slug), 404));
        }

        return $this->handleView($this->view($image, 200, [], ['Default', 'Detail']));
    }

    /**
     * Get image
     *
     * @return Mixed
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image with specific sizes",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getImageGenerateAction(Request $request, $id, $width, $height, $crop)
    {
        if (!$image = $this->getManager()->findBySize($id, $this->getSource(), $width, $height, $crop)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id), 404));
        }

        return $this->handleView($this->view($image, 200, [], ['Default', 'Detail']));
    }

    /**
     * Get image url
     *
     * @return Mixed
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image url",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getImageUrlAction(Request $request, $id)
    {
        $criteria = [
            'id'     => $id,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id), 404));
        }

        $request = $this->get('request');
        $url     = $this->generateUrl('image_get', ['slug' => $image->getSlug(), '_format' => $image->getExtension()]);

        return $this->handleView($this->view($request->getSchemeAndHttpHost() . $url));
    }

    /**
     * Get image url
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image url with specific sizes",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getImageUrlGenerateAction($id, $width, $height, $crop)
    {
       if (!$image = $this->getManager()->findBySize($id, $this->getSource(), $width, $height, $crop)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id), 404));
        }

        $request = $this->get('request');
        $url     = $this->generateUrl('image_get', ['slug' => $image->getSlug(), '_format' => $image->getExtension()]);

        return $this->handleView($this->view($request->getSchemeAndHttpHost() . $url));
    }

    /**
     * Get image childs list
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Get image childs list",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image"
     *     }
     * )
     */
    public function getImageChildAction(Request $request, $id)
    {
        $criteria = [
            'id'     => $id,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id), 404));
        }

        return $this->handleView($this->view($image->getChilds(), 200, [], ['Default', 'Detail']));
    }

    /**
     * Update image info
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Create new image",
     *     statusCodes={
     *         200="Ok",
     *         404="Not found image",
     *         422="Invalid image",
     *         500="Failed to save image"
     *     }
     * )
     */
    public function postImageAction()
    {
        $image = $this->getManager()->createEntity();
        $image->setSource($this->getSource());

        return $this->createEditForm($image);
    }

    /**
     * Create a new image
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Update image",
     *     statusCodes={
     *         200="Ok",
     *         422="Invalid image",
     *         500="Failed to save image"
     *     }
     * )
     */
    public function putImageAction($id)
    {
        $criteria = [
            'id'     => $id,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id)));
        }

        return $this->createEditForm($image);
    }

    /**
     * Remove image
     *
     * @return View
     *
     * @Rest\View()
     * @ApiDoc(
     *  section="Image",
     *  description="Remove image",
     *     statusCodes={
     *         200="Ok",
     *         422="Invalid image",
     *         500="Failed to remove image"
     *     }
     * )
     */
    public function deleteImageAction($id)
    {
        $criteria = [
            'id'     => $id,
            'source' => $this->getSource()
        ];

        if (!$image = $this->getManager()->findOneBy($criteria)) {
            return $this->handleView($this->view(sprintf('Not found image "%s"', $id)));
        }

        if (!$this->getManager()->remove($image)) {
            return $this->handleView($this->view(sprintf('Failed to remove image "%s"', $id)));
        }

        return $this->handleView($this->view('image removed', 200));
    }

}
