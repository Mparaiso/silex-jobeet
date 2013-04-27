<?php
namespace Mparaiso\JobBoard\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\FormBuilderInterface;

class JobType extends AbstractType
{
    protected static $type = array('Full time' => 'Full time',
                                   'Part time' => 'Part time',
                                   'Freelance' => 'Freelance');

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('category', NULL, array('constraints' => array(new NotNull()), 'required' => TRUE))
            ->add("position", "text", array(
            'constraints' => array(new Length(array('min' => 4, "max" => 255)))))
            ->add('company', NULL, array(
            'constraints' => array(new Length(array('min' => 3, 'max' => 255)))))
            ->add('logo_file', "file", array('attr'=>array('accept'=>'image/*'),
            'mapped'=>false,'required' => FALSE, 'constraints' => array(
            new File(array("maxSize" => '300k', "mimeTypes" => array('image/*'))))))
            ->add('url', NULL, array('required' => FALSE, 'constraints' => array(
            new Url())))
            ->add('type', 'choice', array(
            "choices" => self::$type, 'constraints' => array(
                new Choice(array("choices" => self::$type)),
            )))
            ->add('location', NULL, array(
            'constraints' => array(new Length(array('min' => 3, 'max' => 255)))))
            ->add('description', NULL, array(
            'constraints' => array(new Length(array('min' => 3, 'max' => 500)))))
            ->add('howToApply', NULL, array('label' => "How to apply ?"))
            ->add('token')
            ->add('isPublic', NULL, array('label' => 'is public ?'))
            ->add('email', 'email', array('constraints' => array(new Email())));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "job";
    }
}

