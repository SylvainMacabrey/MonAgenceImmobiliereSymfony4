<?php

namespace Sylvain\RecaptchaBundle\Constraints;

use ReCaptcha\ReCaptcha;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;

class RecaptchaValidator extends ConstraintValidator
{
	/**
	* @var RequestStack
	*/
	private $requestStack;

	/**
	* @var ReCaptcha
	*/
	private $reCaptcha;

	public function __construct(RequestStack $requestStack, ReCaptcha $reCaptcha) {
		$this->requestStack = $requestStack;
		$this->reCaptcha = $reCaptcha;
	}

	public function validate($value, Constraint $constraint) {
		$request = $this->requestStack->getCurrentRequest();
		$recaptchaResponse = $request->request->get('g-recaptcha-response');
		if(empty($recaptchaResponse)) {
			$this->addViolation($constraint);
			return;
		}
		$response = $this->reCaptcha
			->setExpectedHostname($request->getHost())
			->verify($recaptchaResponse, $request->getClientIp());
			if(!$response->isSuccess()) {
				dump($response->getErrorsCodes());
				$this->addViolation($constraint);
			}
	}

	private function addViolation(Constraint $constraint) {
		$this->context->buildViolation($constraint->message)->addViolation();
	}
}