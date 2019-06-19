<?php

namespace Sylvain\RecaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface {

	Public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('recaptcha');
		$rootNode
			->children()
			->scalarNode('key')
			->isRequired()
			->end()
			->scalarNode('secret')
			->cannotBeEmpty()
			->isRequired()
			->end()
			->end();
		return $treeBuilder;
	}

}