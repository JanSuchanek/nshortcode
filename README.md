# NShortcode

WordPress-style shortcode parser for Nette Framework — `[prefix:args]` syntax in content.

## Features

- ⌨️ **Shortcode Syntax** — `[gallery:id=5]`, `[youtube:dQw4w9WgXcQ]`
- 🔌 **Custom Handlers** — Register handlers for each shortcode prefix
- 📝 **Content Filter** — Process shortcodes in any text/HTML content
- ⚙️ **DI Extension** — Auto-discovers shortcode handlers

## Installation

```bash
composer require jansuchanek/nshortcode
```

## Configuration

```neon
extensions:
    shortcode: NShortcode\DI\NShortcodeExtension
```

## Usage

Register a handler:

```php
use NShortcode\ShortcodeHandlerInterface;

class YoutubeShortcode implements ShortcodeHandlerInterface
{
    public function getPrefix(): string
    {
        return 'youtube';
    }

    public function handle(string $args): string
    {
        return '<iframe src="https://youtube.com/embed/' . $args . '"></iframe>';
    }
}
```

Process content:

```php
$html = $shortcodeProcessor->process($page->getContent());
// [youtube:dQw4w9WgXcQ] → <iframe src="https://youtube.com/embed/dQw4w9WgXcQ"></iframe>
```

## Requirements

- PHP >= 8.2
- Nette DI ^3.2

## License

MIT
