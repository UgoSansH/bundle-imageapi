<?php

namespace Ugosansh\Bundle\Image\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use JMS\Serializer\SerializationContext;

/**
 * Base controller
 */
class Controller extends FOSRestController
{
    /**
     * @param array $allowedFilters Liste des filtres authorisés dans la requête
     * @param array $filters        Liste des valeurs, si null on utilise la request
     *
     * @throws HttpException quand un paramétre est invalide
     */
    protected function checkQueryParams(array $allowedFilters, $filters = null)
    {
        if (is_null($filters)) {
            $filters = $this->get('request')->query->all();
        }
        foreach ($filters as $filter => $value) {
            if (!array_key_exists($filter, $allowedFilters)) {
                throw new HttpException(400, 'Bad Parameter: "' . $filter . '" is not allowed');
            }
            if ($allowedFilters[$filter] !== null && $value != $allowedFilters[$filter]) {
                if (is_array($value) && is_array($allowedFilters[$filter])) {
                    $badValues = array_values(array_diff($value, $allowedFilters[$filter]));
                    $value     = isset($badValues[0]) ? $badValues[0] : null;
                } elseif (!is_array($value) && is_array($allowedFilters[$filter])) {
                    throw new HttpException(400, 'Bad Parameter: "' . $filter . '" must be an array');
                }
                if (!is_null($value)) {
                    throw new HttpException(400, 'Bad Parameter: "' . $value . '" is not an allowed value for "' . $filter . '"');
                }
            }
        }
    }

    /**
     * Override view method in order to set serialization groups easily
     *
     * @param null  $data
     * @param null  $statusCode
     * @param array $headers
     * @param array $serializationContextGroups
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function view($data = null, $statusCode = null, array $headers = array(), array $serializationContextGroups = array('Default'))
    {
        return
            parent::view($data, $statusCode, $headers)
            ->setSerializationContext(
                SerializationContext::create()->setGroups($serializationContextGroups)
            );
    }
}