<?php

namespace Ace\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
		$builder
			->add('firstname', 'text', array('label' => 'user_registration_form_firstname'))
			->add('lastname', 'text', array('label' => 'user_registration_form_lastname'))
			->add('twitter', 'text', array('label' => 'user_registration_form_twitter',
											'required' => false));
			/* Commented until Mailchimp's DNS issues are resolved
			->add('newsletter', 'checkbox', array(
												'label' => 'user_registration_form_newsletter',
												'attr' => array('checked' => 'checked'),
												'property_path' => false,
												'required' => false,
												)); */
    }

    public function getName()
    {
        return 'ace_user_registration';
    }
}

