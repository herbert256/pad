# Examples

## Introduction

A search box over the harvested examples in `DATA/examples/` - the store the build's
harvest fills with every page's `.php` and `.pad` sources and its rendered `.html`. A hit
opens the example with the two sources on the left, coloured, and the rendered result
beside them; on a narrow screen the result drops below the sources.

The search matches the example's name first and the contents of its three files second,
so `sequence/random` and `upper` both find their way.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | The search box, and the hits as links |
| `show.php/pad` | One example: PHP and PAD sources beside the rendered result |
| `_inits.pad` | The common chrome and the panel styles |
