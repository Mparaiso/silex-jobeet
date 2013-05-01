<?php

namespace Mparaiso\JobBoard\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Created by JetBrains PhpStorm.
 * User: mark prades
 * Date: 28/04/13
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */
class AffiliateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('url')
            ->add('email')
            ->add('isActive')
            ->add('token', NULL, array('required' => FALSE))
            ->add('createdAt')
            ->add('categories', NULL, array('expanded'   => TRUE,
                                            'label_attr' => array('class' => 'inline'),
                                            "attr"       => array('class' => 'inline')));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "affiliate";
    }
}
