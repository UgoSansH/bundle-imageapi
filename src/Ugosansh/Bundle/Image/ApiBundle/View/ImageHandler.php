<?php

namespace Ugosansh\Bundle\Image\ApiBundle\View;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ugosansh\Component\Image\ImageInterface;

/**
 * Fos Rest View Handler
 */
class ImageHandler
{
    /**
     * @var ImageManagerInterface
     */
    protected $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ViewHandler $viewHandler
     * @param View $view
     * @param Request $request
     * @param string $format
     *
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        $format = $request->get('_format') ?: 'json';

        if (($view->getData() instanceof ImageInterface) && ($format != 'json')) {
            $image   = $view->getData();
            $content = $this->manager->getImageSource($image);
            $headers = [
                'Content-Type' => $image->getMimeType()
            ];

            return new Response($content, 200, $headers);
        }

        return $handler->createResponse($view, $request, 'json');
    }

}