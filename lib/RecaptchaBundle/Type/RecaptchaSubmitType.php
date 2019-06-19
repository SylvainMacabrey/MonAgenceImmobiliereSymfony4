<?php

namespace Sylvain\RecaptchaBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylvain\RecaptchaBundle\Constraints\Recaptcha;

class RecaptchaSubmitType extends AbstractType
{
	/**
	* @var string
	*/
	private $key;

	public function __construct(string $key) {
		$this->key = $key;
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			'mapped' => false,
			'contraints' => new Recaptcha()
		]);
	}

	public function buildView(FormView $view, FormInterface $interface, array $options) {
		$view->vars['label'] = false;
		$view->vars['button'] = $options['label'];
		$view->vars['key'] = $this->key;
	}

	public function getBlockPrefix() {
		return 'recaptcha_submit';
	}

	public function getParent() {
		return TextType::class;
	}
}

