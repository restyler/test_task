<?php

namespace Alx\TestTaskBundle\Twig;

use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Alx\TestTaskBundle\Entity\Document;

class FormExtension extends \Twig_Extension
{
    /**
     * @var FormFactory
     */
    private $form_factory;

    /**
     * @var Router
     */
    private $router;

    /**
     * Constructor
     * @param FormFactory $form_factory
     * @param Router $router
     */
    public function __construct(FormFactory $form_factory, Router $router)
    {
        $this->form_factory = $form_factory;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('delete_form', [$this, 'getDeleteForm'])
        ];
    }

    public function getDeleteForm($entity)
    {
        if ($entity instanceof Document) {
            /** @var FormBuilder $form_builder */
            $form_builder = $this->form_factory->createBuilder(FormType::class);
            return $form_builder->setAction($this->router->generate('documents_delete', ['id' => $entity->getId()]))
                                ->setMethod('DELETE')
                                ->getForm()
                                ->createView();
        } else {
            throw new \Twig_Error_Runtime("Unknown entity type: can't create delete form");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form_extension';
    }
}
