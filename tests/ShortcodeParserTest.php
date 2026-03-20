<?php

declare(strict_types=1);

use Tester\Assert;
use NShortcode\ShortcodeParser;

require __DIR__ . '/../../../vendor/autoload.php';
Tester\Environment::setup();

$parser = new ShortcodeParser();

// Test: unknown shortcodes are left as-is
Assert::same('Hello [unknown:test] world', $parser->process('Hello [unknown:test] world'));

// Test: registered handler is called
$parser->register('hello', fn(string $args) => "Hi {$args}!");
Assert::same('Hey Hi Jan! ok', $parser->process('Hey [hello:Jan] ok'));

// Test: multiple shortcodes
Assert::same('Hi A! and Hi B!', $parser->process('[hello:A] and [hello:B]'));

// Test: empty content
Assert::same('', $parser->process(''));

// Test: content without brackets
Assert::same('no shortcodes here', $parser->process('no shortcodes here'));

// Test: handler error returns HTML comment
$parser->register('boom', fn(string $args) => throw new \RuntimeException('fail'));
Assert::contains('<!-- shortcode error:', $parser->process('[boom:test]'));

// Test: hasHandler
Assert::true($parser->hasHandler('hello'));
Assert::false($parser->hasHandler('nope'));

// Test: getRegisteredPrefixes
Assert::contains('hello', $parser->getRegisteredPrefixes());

echo "ShortcodeParser: ALL TESTS PASSED\n";
