<?php

declare(strict_types=1);

namespace NShortcode;

/**
 * Universal shortcode parser for content processing.
 *
 * Parses [prefix:args] syntax and dispatches to registered handlers.
 * Handlers are registered per prefix (e.g., 'promo', 'embed', 'gallery').
 *
 * Usage:
 *   $parser = new ShortcodeParser();
 *   $parser->register('promo', fn(string $args) => '<div>...' . $args . '</div>');
 *   $parser->register('youtube', fn(string $args) => '<iframe src="https://youtube.com/embed/' . $args . '"></iframe>');
 *   echo $parser->process($htmlContent);
 */
final class ShortcodeParser
{
	/** @var array<string, callable(string): string> */
	private array $handlers = [];


	/**
	 * Register a handler for a shortcode prefix.
	 *
	 * @param string $prefix  Shortcode prefix (e.g. 'promo', 'youtube')
	 * @param callable(string): string $handler  Receives the part after prefix, returns HTML
	 */
	public function register(string $prefix, callable $handler): self
	{
		$this->handlers[$prefix] = $handler;
		return $this;
	}


	/**
	 * Process content and replace all [prefix:...] shortcodes.
	 */
	public function process(string $content): string
	{
		if ($content === '' || !str_contains($content, '[')) {
			return $content;
		}

		return preg_replace_callback(
			'/\[([a-zA-Z0-9_-]+):([^\]]*)\]/',
			fn(array $m) => $this->dispatch($m[1], $m[2], $m[0]),
			$content,
		) ?? $content;
	}


	/**
	 * Get all registered prefixes.
	 * @return list<string>
	 */
	public function getRegisteredPrefixes(): array
	{
		return array_keys($this->handlers);
	}


	/**
	 * Check if a handler is registered for a prefix.
	 */
	public function hasHandler(string $prefix): bool
	{
		return isset($this->handlers[$prefix]);
	}


	private function dispatch(string $prefix, string $args, string $original): string
	{
		if (!isset($this->handlers[$prefix])) {
			return $original; // Unknown prefix — leave as-is
		}

		try {
			return ($this->handlers[$prefix])(trim($args));
		} catch (\Throwable $e) {
			return '<!-- shortcode error: ' . htmlspecialchars($prefix . ':' . $args) . ' -->';
		}
	}
}
