# search:und

Language-neutral tokens: lowercase Unicode letters/numbers, retaining interior dot, underscore, colon, hyphen and apostrophe. No stemming, stop words or accent folding. The engine maximum token length is 64 code points. Other punctuation separates tokens. Cache identity includes language code. This is identifier token matching, not byte-exact whole-field matching.

## Example

The paired PHP page supplies the application data:

```php
<?php
$catalog=[['name'=>'PAD-42 identifier','link'=>'/tags/'],['name'=>'Města a městům','link'=>'/tags/']];
```

The PAD template renders it:

```pad
{search:und 'catalog', q='PAD-42', fields='name'}<p>{$name}</p>{/search:und}
```

Run the focused fixture with `php tests/subsystems/search-und.php`.
