<?php

declare(strict_types=1);

namespace NShortcode\Latte;

use Latte\Extension;
use NShortcode\ShortcodeParser;

/**
 * Latte extension that provides a |shortcode filter.
 *
 * Usage in templates:
 *   {$page->getContent()|shortcode|noescape}
 */
final class ShortcodeLatteExtension extends Extension
{
	public function __construct(
		private readonly ShortcodeParser $parser,
	) {
	}


	public function getFilters(): array
	{
		return [
			'shortcode' => $this->filterShortcode(...),
		];
	}


	public function filterShortcode(string $content): string
	{
		return $this->parser->process($content);
	}
}
