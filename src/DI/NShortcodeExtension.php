<?php

declare(strict_types=1);

namespace NShortcode\DI;

use NShortcode\Latte\ShortcodeLatteExtension;
use NShortcode\ShortcodeParser;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Nette DI Extension for NShortcode.
 *
 * Registers ShortcodeParser as a service and optionally adds the Latte filter.
 *
 * Config:
 *   shortcode:
 *       latte: true  # Register |shortcode Latte filter (default: true)
 */
final class NShortcodeExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'latte' => Expect::bool(true),
		]);
	}


	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('parser'))
			->setFactory(ShortcodeParser::class);
	}


	public function beforeCompile(): void
	{
		/** @var \stdClass $config */
		$config = $this->config;
		$builder = $this->getContainerBuilder();

		if ($config->latte) {
			$builder->addDefinition($this->prefix('latteExtension'))
				->setFactory(ShortcodeLatteExtension::class);

			$latteFactory = $builder->getDefinitionByType(\Nette\Bridges\ApplicationLatte\LatteFactory::class);
			/** @var \Nette\DI\Definitions\FactoryDefinition $latteFactory */
			$latteFactory->getResultDefinition()
				->addSetup('addExtension', [$this->prefix('@latteExtension')]);
		}
	}
}
