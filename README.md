# zenstruck/commonmark-extensions

## Installation

```bash
composer require zenstruck/commonmark-extensions
```

## GFM Admonitions (Notes)

The `AdmonitionExtension` adds support for [GFM style admonitions](https://github.com/orgs/community/discussions/16925).

Enable the extension:

```php
use League\CommonMark\Environment\Environment;
use Zenstruck\CommonMark\Extension\GitHub\AdmonitionExtension;

/** @var Environment $environment */

$environment->addExtension(new AdmonitionExtension());
```

The following markdown:

```markdown
> [!NOTE] <!-- Can also use "TIP", "IMPORTANT", "WARNING", "CAUTION" -->
> Admonition content...
```

Renders as:

```html
<blockquote class="md-admonition md-admonition-note" role="alert">
    <p class="md-admonition-label">Note</p>
    <p>
        Admonition content...
    </p>
</blockquote>
```

> [!NOTE]
> See [this sample CSS file](doc/github-admonitions.css) to style the admonitions similar to GitHub.
