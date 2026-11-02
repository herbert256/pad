# embed:sandbox

Trusted application HTML as the first parameter, title required. `height=80..2000` pixels (default 240), maximum source 256 KiB. Empty sandbox grants no scripts, same-origin, forms, popups or top navigation. Explicit scripts=1 grants only scripts; same-origin and parent navigation remain forbidden. A first CSP meta denies network, objects, forms and base URLs, permitting inline styles/data images and optional inline scripts. Parent CSP may further restrict this. HTML remains unsanitized inside its isolated frame. Escaped source is the accessible text fallback. [WHATWG iframe policy](https://html.spec.whatwg.org/multipage/iframe-embed-object.html#attr-iframe-sandbox).

## Example

```pad
{embed:sandbox '<h2>Hello</h2><p>Isolated demonstration.</p>', title='HTML demo', height=180/}
```

Run the focused fixture with `php tests/subsystems/embed-sandbox.php`.
