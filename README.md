# medas-html-templates

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

An XML/DOM-based server-side HTML templating engine. Templates are well-formed XML strings processed by a pipeline of `MarkUpHandler` implementations that run in priority order. The entry point is `TemplateCompiler::compile()`, which loads the template into a `DOMDocument`, applies all handlers, and serialises the result back to an HTML string.

**Mark-up handlers** (in execution order):

| Handler                   | Priority | What it does                                                                                                |
|---------------------------|----------|-------------------------------------------------------------------------------------------------------------|
| `ParentHandler`           | 100      | Injects the child template into the parent's placeholder tag (`<children>` by default) and merges variables |
| `ForEachHandler`          | −200     | Expands `m-foreach` / `m-as` attribute pairs into repeated DOM nodes                                        |
| `IfHandler`               | −300     | Keeps or removes elements based on a `m-if` expression                                                      |
| `InterpolationHandler`    | −400     | Replaces `{{ expression }}` tokens in text content and attributes                                           |
| `ContainerHandler`        | −500     | Unwraps `<m-container>` elements, keeping their children                                                    |
| `UntrueAttributesRemover` | −600     | Evaluates `checked` and `selected` attributes; removes them when falsy                                      |

All attribute names and element prefixes use a configurable prefix (default `m-`). Expressions are evaluated by `StringEvaluator` using `eval()` in a sandboxed local scope — **templates must come from trusted developer-written sources only**.

`ScssAdder` compiles SCSS strings to CSS (via `scssphp/scssphp`) and injects them into a `<style id="…"></style>` placeholder in the template string before parsing.

## Configuration options

| Option                                   | Default | Description                                                                                                                                            |
|------------------------------------------|---------|--------------------------------------------------------------------------------------------------------------------------------------------------------|
| `html-templates.attribute-prefix`        | `m-`    | Prefix for all template-specific attributes and elements                                                                                               |
| `html-templates.default-parent-template` | `null`  | Class name of a `HasDefaultParentTemplate` service; its `defaultParentTemplate()` is used as the layout for all templates that have no explicit parent |

## Usage

### Package developer context

Register the package and inject `TemplateCompiler`:

```php
use Medas\HtmlTemplates\HtmlTemplatesPackage;

HtmlTemplatesPackage::instance();
```

**Compiling a template:**

```php
use Medas\HtmlTemplates\TemplateCompiler;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\Core\Attributes\Service;

#[Service]
readonly class PageRenderer
{
    public function __construct(
        private TemplateCompiler $templateCompiler,
    ) {}

    public function render(string $templateString, array $variables = []): string
    {
        return $this->templateCompiler->compile(
            new HtmlTemplate(
                template: $templateString,
                variables: $variables,
            ),
        );
    }
}
```

**Variable interpolation — `{{ expression }}`:**

```html
<p>Hello, {{ $name }}!</p>
<p>Total: {{ $count * $price }}</p>
<a href="{{ $user->profileUrl() }}">Profile</a>
```

Any PHP expression is valid. Variables are scoped to the template's `$variables` array.

**Conditional rendering — `m-if`:**

```html
<!-- Element is kept when the expression is truthy, removed when falsy -->
<div m-if="$isAdmin">Admin panel</div>
<span m-if="$count > 0">{{ $count }} items</span>
```

**Looping — `m-foreach` and `m-as`:**

```html
<!-- m-foreach: expression that resolves to an iterable -->
<!-- m-as: variable name to bind each value to -->
<ul>
    <li m-foreach="$items" m-as="$item">{{ $item['name'] }}</li>
</ul>
```

Nested loops work — each level gets a unique internal variable to prevent name collisions.

**Wrapper elements — `<m-container>`:**

Use `<m-container>` when you need to group elements without adding a DOM wrapper:

```html
<m-container>
    <p>First paragraph</p>
    <p>Second paragraph</p>
</m-container>
<!-- Renders as two <p> elements with no wrapper -->
```

**Boolean attributes — `checked` and `selected`:**

```html
<!-- Attribute is kept with value="checked" when truthy, removed when falsy -->
<input type="checkbox" checked="{{ $isActive }}" />
<option value="nl" selected="{{ $country === 'nl' }}">Netherlands</option>
```

**Parent / layout templates:**

```php
$layout = new HtmlTemplate(
    template: '<html><body><children /></body></html>',
);

$page = new HtmlTemplate(
    template: '<main><h1>Welcome</h1></main>',
    parent: $layout,
);

$html = $templateCompiler->compile($page);
// <html><body><main><h1>Welcome</h1></main></body></html>
```

The child's root element replaces the `<children>` placeholder. The placeholder tag name is configurable per template via `$parentPlaceholderTag`.

**Default parent template** — implement `HasDefaultParentTemplate` on a service and register it via the config option so every template is automatically wrapped in the layout:

```php
use Medas\HtmlTemplates\Templates\{HasDefaultParentTemplate, HtmlTemplate};
use Medas\Core\Attributes\Service;

#[Service]
readonly class AppLayout implements HasDefaultParentTemplate
{
    public function defaultParentTemplate(): HtmlTemplate
    {
        return new HtmlTemplate(
            template: file_get_contents(__DIR__ . '/layout.html'),
        );
    }
}
```

```yaml
html-templates:
  default-parent-template: MyApp\Templates\AppLayout
```

**SCSS injection:**

```php
use Medas\HtmlTemplates\{ScssAdder, Templates\HtmlTemplate};

$template = new HtmlTemplate(
    template: '<html><head><style id="page-styles"></style></head><body>…</body></html>',
);

// Compiles SCSS and replaces the matching <style id="page-styles"></style> placeholder
$scssAdder->addScss($template, 'page-styles', '
    $primary: #3498db;
    body { background: $primary; }
');
```

**Custom mark-up handler:**

```php
use Medas\HtmlTemplates\MarkUpHandlers\MarkUpHandler;
use Medas\HtmlTemplates\Templates\HtmlTemplate;
use Medas\Core\Attributes\Service;

#[Service]
readonly class TranslationHandler implements MarkUpHandler
{
    public function __construct(
        private Translator $translator,
    ) {}

    public function priority(): int
    {
        // Run after interpolation (−400) so variables are already resolved
        return -450;
    }

    public function handle(HtmlTemplate $template): void
    {
        // Walk the DOM and replace <m-t key="some.key" /> with translated strings
        $nodes = $template->dom->getElementsByTagName('m-t');

        while ($nodes->length > 0) {
            $node = $nodes->item(0);
            $key  = $node->getAttribute('key');
            $text = $template->dom->createTextNode($this->translator->translate($key));

            $node->parentNode->replaceChild($text, $node);
        }
    }
}
```

Custom handlers are discovered automatically via the service container — no manual registration needed.

### Backend user context

**Configuring the attribute prefix:**

```yaml
html-templates:
  attribute-prefix: "x-"
```

All template attributes and wrapper elements then use `x-` instead of `m-` (e.g. `x-if`, `x-foreach`, `<x-container>`).

**Security note** — `StringEvaluator` uses `eval()`. Template strings and variable values must originate from server-side developer code, not from user input. Never pass unsanitised user data as a template string or as a variable value that feeds into an `m-if` or `{{ }}` expression.

**Template parsing** — templates must be well-formed XML. A single root element is required; if none is present the compiler wraps the content in `<m-container>` automatically and strips it at the `ContainerHandler` stage. `InvalidTemplateException` is thrown for XML that cannot be parsed.
