<?php

namespace Ugosansh\Bundle\Image\ApiBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ugosansh\Component\Image\Upload\Uploader;
use Ugosansh\Component\Image\ImageInterface;

/**
 * Form handler application
 */
class FormHandler
{
    /**
     * @var array
     */
    protected $methods;

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * __construct
     *
     * @param array $methods Allowed methods
     */
    public function __construct(array $methods = [])
    {
        $this->methods = empty($methods) ? ['POST', 'PUT'] : $methods;
    }

    /**
     * validate
     *
     * @param Form    $form    Form
     * @param Request $request Request
     *
     * @return boolean
     */
    public function validate(Form $form, Request $request)
    {
        if ($this->hasMethod($request->getMethod())) {
            if ($request->getMethod() != 'POST') {
                $form->submit($request);
            } else {
                $form->handleRequest($request);
            }

            return $form->isValid();
        }

        return false;
    }

    /**
     * processing validation
     * By default, validate form
     *
     * @param Form    $form    Form
     * @param Request $request Request
     *
     * @return mixed
     */
    public function process(Form $form, Request $request)
    {
        return $this->validate($form, $request);
    }


    /**
     * Get array of form errors
     *
     * @param Form $form
     *
     * @return array
     */
    public function getErrors(Form $form) {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrors($child);
            }
        }

        return $errors;
    }

    /**
     * addMethod
     *
     * @param string $method
     *
     * @return FormHandler
     */
    public function addMethod($method)
    {
        if (!$this->hasMethod($method)) {
            $this->methods[] = $methods;
        }
    }

    /**
     * hasMethod
     *
     * @param string $method
     *
     * @return boolean
     */
    public function hasMethod($method)
    {
        return in_array($method, $this->methods);
    }

    /**
     * setMethods
     *
     * @param array $methods
     *
     * @return FormHandler
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * getMethods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
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

}