# PAD Tag Types Reference

This document provides a complete reference for all PAD tag types.

### Subsystem selectors

Subsystem tags use their own prefix: `{chart:bar .../}`, `{widget:copy 'text'/}`,
`{barcode:qr 'text'/}` and `{notation:chess 'fen'/}`. The old `pad:` dispatch wrappers
and aliases have been removed. The kind is part of the name, so the first parameter
belongs to that kind.

A variable may select a registered kind inside a fixed subsystem: `{icon:$icon/}`
or `{widget:$kind}...{/widget:$kind}`. The closing tag repeats the variable name.
This reads one field as a kind name; it does not interpret its value as template code
or allow it to select a different namespace. Variable output such as `{$name}` is unchanged.

`{barcode:auto $code/}` chooses EAN-13 for twelve or thirteen digits and Code 128
otherwise. `{embed:auto $url, title='Video'/}` detects an embed provider from its
address. `{diagram:auto}...{/diagram:auto}` detects a Mermaid header, defaulting to
a flowchart. Explicit kinds work as before.

`{fake:rows 'customer:name, email, total:price', rows=3, seed=7}...{/fake:rows}`
generates several fields per row. Single kinds use `{fake:email/}`.

Basic `{pad:input}` and helper-only subsystem tags retain their distinct behavior.

### Sequence selectors

Sequence generation and transformations use their own namespaces. The `sequence`,
`action`, `keep`, `make` and `remove` dispatch tags have been removed from `pad/tags`.

```pad
{sequence:range '1..10'}{$sequence} {/sequence:range}
{sequence:list '3;1;2'}{$sequence} {/sequence:list}
{sequence:loop 5}{$sequence} {/sequence:loop}
{sequence:fibonacci rows=8}{$sequence} {/sequence:fibonacci}
{action:reverse '1..5'}{$sequence} {/action:reverse}
{keep:prime '1..10'}{$sequence} {/keep:prime}
{make:add 3, rows=5}{$sequence} {/make:add}
{remove:prime '1..10'}{$sequence} {/remove:prime}
```

A variable containing a registered kind can select it inside a fixed namespace:
`{sequence:$kind rows=5}...{/sequence:$kind}`. Kind and store names retain their case,
including `newmanConway`. The closing tag repeats the variable name.

Use `auto` when the parameter's value or written order determines the source:
`{sequence:auto $source}...{/sequence:auto}` accepts a count, range, list, stored
sequence or type name. The same selector is available as `action:auto`, `keep:auto`,
`make:auto` and `remove:auto`; their namespaces retain the operation's meaning.
For example, `{keep:auto $values, $test}...{/keep:auto}` keeps the selected values
that match the runtime test. An invalid name is still an error.

`{$sequence}` remains the common current-value field. A property addressing a loop
by its tag name uses the new name, such as `last@range` inside `{sequence:range}`.


## Required syntax

Every named PAD tag has an explicit type prefix: `{type:tag}`. A pair closes with
that same qualified name; a single tag ends with `/`.

```pad
{pad:if $ready}Ready{pad:else}Waiting{/pad:if}
{array:orders}<li>{$name}</li>{/array:orders}
{data:orders}<li>{$name}</li>{/data:orders}
{app:card title='Summary'}Content{/app:card}
{chart:pie data='sales'/}
{function:upper}hello{/function:upper}
```

The prefix selects one namespace directly. Application tags cannot shadow `pad:`
tags, and a variable called `pie` cannot replace `chart:pie`. Bare named tags do
not execute. Strict mode reports a missing prefix; lenient mode retains the text.
`optional` and `noError` do not make a missing prefix valid under strict mode.

Variable and expression sigils are unchanged: `{$name}`, `{!html}`, `{?query}`,
`{#parameter}`, `{&property}` and `{^json}`. Expressions and pipe functions retain
their own lookup rules: `{pad:echo $name | upper/}` still uses the `upper` pipe.

Use `pad:` for built-in controls, including `pad:else`, `pad:elseif`, `pad:when`,
`pad:meta`, `pad:extends`, `pad:block` and `pad:slot`. Use `app:` for application
`_tags`, `common:` for shared tags and snippets, and `include:` for application
`_include` files. Stored values use `data:`, `content:`, `bool:`, `define:`,
`macro:`, `select:` or `pull:`; PHP arrays use `array:`.

Sequence operations are type first: `{keep:prime ...}` and `{make:fibonacci ...}`.
Put a stored sequence in `pull=`, for example
`{action:reverse pull='numbers'}{$sequence}{/action:reverse}`. A store or sequence
name before the colon is no longer a type prefix.


## Tag Types

### define

A tag declared earlier in the template with `{pad:define}`. Its body is the tag's template:
parameters are read as `{#name}`, and a pair's content goes to `@content@`. Named slots work
as they do in an application's `_tags/` file. Use the `define:` prefix to select the definition;
`define:name` explicitly selects it.

```pad
{pad:define 'greeting'}<p>Hello, {#name}!</p>{/pad:define}
{define:greeting name='Ann'/}
{define:greeting name='Bob'/}
```

The definition takes effect when it is walked and prints nothing. Each use renders its body
in the caller's context. A definition must have a name and body. The required `define:` prefix
keeps it independent of built-in, application and macro tags with the same name.

**Handler:** `pad/types/define.php`; shared implementation: `pad/tags/_lib/macro.php`.

### macro

A tag declared earlier with `{pad:macro}`. Its declared parameters become fields of one row,
and its body renders once for each use. Required parameters have no default; optional ones
have `=default`. Values can be supplied positionally or by name. The explicit `macro:name`
form selects the macro explicitly.

```pad
{pad:macro 'entry', label, text='No text.'}<p><b>{$label}</b>: {$text}</p>{/pad:macro}
{macro:entry label='One', text='First entry.'/}
{macro:entry 'Two'/}
```

The caller's fields remain visible behind the macro's parameters. Pair content and slots
render in that row too. Values remain text, not template source. Missing required values,
extra positional values, unknown named parameters and parameters that collide with handling
options are errors under the strict check.

**Handler:** `pad/types/macro.php`; shared implementation: `pad/tags/_lib/macro.php`.

### app

Loads application-specific tags from `_tags/` in the page's directory or a parent's, up to the
application root.

```
{app:myCustomTag/}
{app:myCustomTag}Content{/app:myCustomTag}
```

**Resolution:** Checks `padAppTagCheck()` for tag in application directory.

**Files loaded:**
- `_tags/myCustomTag.php` - PHP logic
- `_tags/myCustomTag.pad` - PAD template

**Use case:** Application-specific custom tags.

---

### pad

Loads built-in PAD tags from the `PAD/tags/` directory.

```
{pad:if $ready}Ready{pad:else}Waiting{/pad:if}
```

**Resolution:** `PAD/tags/{tagName}.php` or `.pad`

**Files loaded:**
- `PAD/tags/{name}.php` - PHP logic
- `PAD/tags/{name}.pad` - PAD template

**Use case:** Core PAD framework tags.

---

### common

Loads shared tags from the `apps/_common/` application.

```
{common:padFrame}
{common:padFrame/}
```

**Resolution:** `padCommonCheck()` - selects the shared namespace independently of `app:` and `pad:`. Can be disabled per app with `$padCommon = FALSE`.

**Use case:** Tags shared by all applications - the frame of the PAD applications, `{common:example}`, `{common:demo}`, `{common:table}`.

---

### data

Retrieves data from the data store (`$padDataStore`).

```
{data:myData/}
{data:myData}  ← If myData exists in data store
```

**Resolution:** `$padDataStore[$tagName]`

**Options:**
- `print` - Output with formatting

**Example:**
```
{array:users toData="activeUsers"}
{data:activeUsers/}
```

---

### content

Retrieves content from the content store (`$padContentStore`).

```
{content:header/}
```

**Resolution:** `$padContentStore[$tagName]`

**Example:**
```
{include:header toContent="savedHeader"}
{content:savedHeader/}
```

---

### bool

Retrieves boolean values from the boolean store (`$padBoolStore`).

```
{bool:isAdmin/}
```

**Resolution:** `$padBoolStore[$tagName]`

**Returns:** The stored boolean value.

**Example:**
```
{app:checkAdmin toBool="isAdmin"}
{pad:if bool:isAdmin}...{/pad:if}
```

---

### field

Retrieves field values from the current data context.

```
{field:username/}
{$username}  ← Shorthand for field
```

**Resolution:** `padFieldValue($tagName)`

**Returns:** Field value, or `NULL` if field is null.

**Example:**
```
{array:users}{field:email/}{/array:users}
{array:users}{$email}{/array:users}  ← Equivalent
```

---

### property

Retrieves tag properties from parent tags.

```
{property:current/}
{&current}  ← Shorthand for property
```

**Resolution:** `padTagValue($tagName)`

**Supports:** Parameter prefixing with `$padParm`.

**Example:**
```
{array:users}{&current}{/array:users}
```

---

### array

Retrieves array values by name.

```
{array:myArray}
```

**Resolution:** `padArrayValue($tagName)`

**Use case:** Access named arrays in the current context.

---

### parm

Retrieves a parameter or option of the tag directly around it - an `{pad:if}` or `{pad:case}` in
between is skipped, any other tag is not: inside `{array:users mode="fast"}{array:orders}` the `{array:orders}` is
asked, and has no `mode`.

```
{array:users mode="fast"}{parm:mode/}{/array:users}
```

**Resolution:** `padOptValue($tagName, 1)`

---

### level

Retrieves an array field of an enclosing row - the nearest level outward that holds one - and
iterates it.

```
{array:orders}{level:lines}{$product}{/level:lines}{/array:orders}
```

**Resolution:** `padGetLevelArray($tagName)`

**Use case:** Walk a nested array of the row being rendered. A `{function:tag $name = value}` level
variable is not what it reads.

---

### constant

Retrieves PHP constants by name.

```
{constant:PHP_VERSION/}
{constant:APP/}
```

**Resolution:** `constant($tagName)`

**Returns:** The value of the PHP constant.

**Example:**
```
{constant:PHP_EOL/}
{constant:TRUE/}
```

---

### local

Loads local data files with automatic type detection.

```
{local:users.json/}
{local:myfile/}            ← _data/myfile.csv, the extension found for it
```

**Resolution:** `padDataFileName($tagName)` - a file directly in `_data/` (a name holds no `/`),
with its extension or with the first of `.xml`, `.json`, `.yaml`, `.csv`, `.php`, `.curl`,
`.sql` that exists. The prefix is required whether or not the extension is given.

**Supported formats:**
- `.php` - Executes PHP and returns result
- `.json` - Parsed as JSON
- `.yaml` - Parsed as YAML
- `.csv` - Parsed as CSV
- `.xml` - Parsed as XML
- `.curl` - Fetches the URL it names
- `.sql` - Runs the named query
- `.toml`, `.ini`, `.tsv`, `.ndjson`, `.md`, `.ics`, `.vcf`, `.geojson`, `.gpx`, `.kml`, `.vtt`, `.srt`, `.xlsx` ... - the
  extension of a format of `pad/subs/formats/` that reads, tried after the extensions above
  ([Data formats](../FEATURES.md#data-formats)); an `.xlsx` is read as its bytes, `sheet=`
  picking a sheet
- Other - an error, unless `type=` names the format

**Options:**
- `name` - Override data name
- `type` - Override file type
- `sandbox` - Enable sandboxed processing

**Example:**
```
{local:settings.json/}
{local:data.csv name='myData', type='csv'/}
```

---

### include

Includes content from application include files.

```
{include:header/}
```

**Resolution:** `padAppIncludeCheck($tagName)`

**Returns:** Included file content.

---

### select

Queries database tables declared in the PAD Select subsystem.

```
{select:users/}
{select:users}  ← If 'users' is declared in $padSelect
```

**Resolution:** `isset($padSelect[$tagName])` - tables are declared in the app's `_lib/select.php` via `$padSelect` (and relations via `$padRelations`).

**Returns:** Query results as iterable data; nested select tags join automatically through `$padRelations`.

**Example:**
```
{select:users $id=5}
  {$username}
{/select:users}
```

See [DATABASE.md](../DATABASE.md) for the full Select subsystem reference.

---

### function

Executes PAD functions as tags.

```
{function:trim}content{/function:trim}
{function:trim}content{/function:trim}
```

**Resolution:** `padFunctionAsTag($tagName)`

**Behavior:**
- Supports start/end tag pairs
- Content is passed as input value
- Positional parameters become the function's arguments

**Example:**
```
{function:upper}hello{/function:upper}  →  HELLO
{function:replace 'a', 'b'}aaa{/function:replace}  →  bbb
```

---

### php

Calls PHP built-in functions directly.

```
{php:strtoupper 'hello'/}
{php:date 'Y-m-d'/}
```

**Resolution:** `function_exists($tagName)`

**Implementation:** `call_user_func_array($tagName, $parameters)`

**Example:**
```
{php:strlen 'hello'/}  →  5
{php:array_sum [1,2,3]/}  →  6
```

---

### script

Executes external shell scripts.

```
{script:myscript/}
{script:processor 'arg1', 'arg2'/}
```

**Resolution:** `padScriptCheck($tagName)` - `_scripts/<name>`, or `_scripts/<name>.<ext>`, from
the page's directory up to the application root.

**Features:**
- Positional arguments, separated by commas, are escaped with `escapeshellarg()`
- Captures stdout output
- Error code checking and reporting

**Returns:** Script output (stdout).

---

## Chart Types

### chart

A chart of the kind the tag names - the chart subsystem in `PAD/subs/charts/` draws it.

```
{chart:pie data='budget', label='post', value='amount'/}
{chart:bar data='sales', label='month', value='amount', stacked/}
{chart:gauge value=72, unit='%'/}
```

**Resolution:** a file `PAD/subs/charts/types/<name>.php` (`padChartType()` in `PAD/lib/chart.php`):
the files there are the kinds, and a kind is added by adding its file. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Game Types

### game

A little game played in the browser, of the kind the tag names - the games subsystem in
`PAD/subs/games/` sets it up.

```
{game:memory pairs=6/}
{game:sudoku givens=26/}
{game:hangman}lighthouse{/game:hangman}
```

**Resolution:** a PHP file `PAD/subs/games/types/<name>.php` (`padGameType()` in `PAD/lib/game.php`),
its script and style `www/assets/games/<name>.js` and `.css`: the files of `types/` are the
games, and a game is added by adding its files. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Diagram Types

### diagram

A diagram of the kind the tag names, drawn from the lines between its tags - the diagram
subsystem in `PAD/subs/diagrams/` lays it out. Use `{diagram:auto}` to detect the kind from a Mermaid header.

```
{diagram:flowchart direction='LR'}A[Order] --> B{Paid?}{/diagram:flowchart}
{diagram:sequence}Shop ->> Bank: Charge{/diagram:sequence}
{diagram:flowchart}A --> B{/diagram:flowchart}
```

**Resolution:** a file `PAD/subs/diagrams/types/<name>.php` (`padDiagramType()` in
`PAD/subs/diagrams/_lib/diagram.php`): the files there are the kinds, and a kind is added by adding
its file. Every named tag requires its type prefix; other namespaces do not shadow it.

### embed

A video, a sound, a map, a pen or a document of the provider the tag names, behind a
click-to-load facade - the embed subsystem in `PAD/subs/embeds/` writes it, as the `{embed:auto}` tag
writes the provider that knows its address. Named, a provider reads its own bare ids too.

```
{embed:youtube 'aqz-KE-bpKQ', title='Big Buck Bunny'/}
{embed:vimeo '76979871', title='The new Vimeo player'/}
{embed:openstreetmap '52.3731,4.8922', zoom=15, title='Dam Square'/}
```

**Resolution:** a file `PAD/subs/embeds/types/<name>.php` (`padEmbedProviders()` in `PAD/lib/embed.php`):
the files there are the providers, and a provider is added by adding its file. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Fake Types

### fake

A fake value of the kind the tag names - the fake subsystem in `PAD/subs/fakes/` makes it. Use `{fake:rows 'name, email', rows=3/}` for several fields per row.

```
{fake:email/}
{fake:number from=1, to=6/}
{fake:date from='2026-01-01', to='2026-12-31', format='j M Y'/}
```

**Resolution:** a file `PAD/subs/fakes/types/<name>.php` (`padFakeKinds()` in `PAD/lib/fake.php`): the
files there are the kinds, and a kind is added by adding its file. The `fake:` prefix is required; `{field:email/}` selects a field instead. `{fake:zebra/}` is "there is no fake kind named 'zebra'".
## Barcode Types

### barcode

A barcode of the symbology the tag names - the codes subsystem in `PAD/subs/codes/` encodes and
draws it. Use `{barcode:auto}` to select EAN-13 for twelve or thirteen digits and Code 128 otherwise.

```
{barcode:ean13 '400638133393'/}
{barcode:datamatrix $serial, size=120/}
{barcode:itf14 '1540014128876'/}
```

**Resolution:** a file `PAD/subs/codes/types/<name>.php` (`padKinds ('codes')`): the files there are
the symbologies - `qr` among them, which the `{barcode:qr}` tag writes by a name of its own - and a
symbology is added by adding its file and its encoder in `PAD/subs/codes/_lib/`. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Icon Types

### icon

An icon of PAD's own set, selected after the colon: `{icon:star}` draws a star
using the icon subsystem in `PAD/subs/icons/`.

```
{icon:star size=20/}
{icon:arrow-right label='Next page'/}
```

**Resolution:** a file `PAD/subs/icons/types/<name>.svg` (`padIconNames ()` in `PAD/lib/icon.php`):
the files there are the icons, and an icon is added by adding its file. The type is only ever
written with its prefix - an icon's name alone is no tag, the names being those of fields and
pages as well.

## Pattern Types

### pattern

A seeded SVG background or piece of generative art, the kind the tag names - the pattern
subsystem in `PAD/subs/patterns/` draws it, the same bytes for the same options.

```
{pattern:topography seed=7, levels=16/}
{pattern:dots size=24, as='url'/}
```

**Resolution:** a PHP file `PAD/subs/patterns/types/<name>.php` (`padPatternKinds ()` in
`PAD/lib/pattern.php`) that answers its default size and colours, its own options and the
function that draws it: the files there are the patterns, and a pattern is added by adding its
file. The type is only ever written with its prefix - a pattern's name alone (dots, grid, waves)
is no tag, the names being those of fields and pages as well.
## Toy Types

### toy

A small live simulation or visual toy played in the browser, of the kind the tag names - the toys
subsystem in `PAD/subs/toys/` sets it up.

```
{toy:life pattern='gun'/}
{toy:mandelbrot zoom=40, center='-0.745, 0.11'/}
{toy:lsystem 'dragon', iterations=14/}
```

**Resolution:** a PHP file `PAD/subs/toys/types/<name>.php` (`padToyType()` in `PAD/lib/toy.php`), its
script `www/assets/toys/<name>.js` and its style, when it has one, `<name>.css` beside it: the files
of `types/` are the toys, and a toy is added by adding its files. Every named tag requires its type prefix; other namespaces do not shadow it.

## Widget Types

### widget

An interactive part of the page - tabs, an accordion, a carousel, a modal, a copy box, a poll
... - of the kind the tag names: the widgets subsystem in `PAD/subs/widgets/` builds it.

```
{widget:tabs active=2}{pad:tab 'Overview'}...{/pad:tab}{pad:tab 'Specs'}...{/pad:tab}{/widget:tabs}
{widget:modal 'terms', title='Terms of sale'}...{/widget:modal}
{widget:tabs}{pad:tab 'Overview'}...{/pad:tab}{/widget:tabs}
```

**Resolution:** a PHP file `PAD/subs/widgets/types/<name>.php` (`padWidgetType()` in `PAD/lib/widget.php`),
the widget's tag code; its functions in `PAD/subs/widgets/_lib/<name>.php`, its style and script in
`www/assets/widgets/<name>.css` and `.js`. The files of `types/` are the widgets, and a widget is
added by adding its files. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Input Types

### input

A rich form field of the kind the tag names - a slider, a stepper, a date picker, stars, a
signature ... - the inputs subsystem in `PAD/subs/inputs/` writes it, a field of its `{pad:form}` as
`{pad:input}` is: a native field a form posts without any script, made more of by its script.

```
{input:slider 'volume', label='Volume', unit='%', rules='integer|between:10,90'/}
{input:datepicker 'arrival', label='Arrival', min='2026-01-01'/}
{input:stars 'score', label='Score'/}
```

**Resolution:** a PHP file `PAD/subs/inputs/types/<name>.php`, its script and style in
`www/assets/inputs/`, the field written by `PAD/subs/inputs/input.php`: the files of `types/` are
the kinds, and a kind is added by adding its file. Its items are read raw, as `{pad:input}`'s are -
`label=`, `rules=`, `min=` are the field's, never an option of the engine. Every named tag requires its type prefix; other namespaces do not shadow it.

## Sound Types

### sound

A short sound made from a recipe, of the kind the tag names - the sounds subsystem in
`PAD/subs/sounds/` writes its button or its binding, and the browser renders and plays it.

```
{sound:chime/}
{sound:success on='click', target='#save'/}
{sound:bell download/}
```

**Resolution:** a PHP file `PAD/subs/sounds/types/<name>.php` that returns the recipe
(`padSoundKinds()` in `PAD/lib/sound.php`): the files there are the sounds, and a sound is added by
adding its file. The `sound:` prefix is required; click, error and pop may also name fields
and pages. `{sound:zigzag/}` is "there is no sound named 'zigzag'".

## Printable Types

### printable

Pages made for paper, of the kind the tag names - sheets of labels, badges, tickets, a letter -
laid out by the printables subsystem in `PAD/subs/printables/`: the content between the tags one item, repeated over the data.

```
{printable:labels data='products', sheet='a4-3x8'}<b>{$name}</b>{/printable:labels}
{printable:tickets data='seats'}<h2>{$show}</h2><aside>{barcode:qr $code/}</aside>{/printable:tickets}
```

**Resolution:** a PHP file `PAD/subs/printables/types/<name>.php` that answers its plan
(`padPrintableKinds()` in `PAD/lib/printable.php`): the files there are the kinds, and a kind is
added by adding its file. The `printable:` prefix is required; labels, cards and letter may also be the
names of fields and pages. `{printable:zigzag}...{/printable:zigzag}` is "there is no printable
named 'zigzag'".

## Notation Types

### notation

A text written in a notation and drawn on the server - a formula in AsciiMath, a chemical
equation, a tune in ABC, a guitar chord, piano keys, braille, morse, semaphore, signal flags, a
chess, go or draughts position, a guitar tab, a Karnaugh map, a bridge deal, a juggling pattern, the moves of a cube - of the kind the tag names: the notations subsystem in
`PAD/subs/notations/` draws it.
The text is the first parameter, or the content between the tags taken as it stands.

```
{notation:math}x = (-b +- sqrt(b^2 - 4ac))/(2a){/notation:math}
{notation:chord 'Am', instrument='ukulele'/}
{notation:braille 'Hello'/}
{notation:chess $fen/}
```

**Resolution:** a PHP file `PAD/subs/notations/types/<name>.php`, its functions in
`PAD/subs/notations/_lib/<name>.php` and its style in `www/assets/notations/<name>.css`: the files
of `types/` are the notations, and a notation is added by adding its files. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## Section Types

### section

A block of a landing page - a hero, the features, the prices, the questions, a footer ... - of the
kind selected after the colon: the sections subsystem in `PAD/subs/sections/`
builds a hero with `{section:hero}`.

```
{section:hero title='Pages that drive themselves', button='Start', link='?start'}The text.{/section:hero}
{section:pricing data='plans'/}
{section:faq}{section:item 'Is it free?'}Yes.{/section:item}{/section:faq}
```

**Resolution:** a PHP file `PAD/subs/sections/types/<name>.php` (`padKinds ( 'sections' )`,
`PAD/lib/subsystem.php`), its style `www/assets/sections/<name>.css`: the files of `types/` are
the sections, and a section is added by adding its files. Only ever written with its prefix.

---

## Effect Types

### effect

Motion or decoration on a text or a block - a typewriter, a marker stroke, a count-up, a
reveal on scroll, a loader, confetti ... - of the kind the tag names: the effects subsystem in
`PAD/subs/effects/` writes it.

```
{effect:typewriter}Hello, world{/effect:typewriter}
{effect:countup to=1250, suffix='+'/}
{effect:highlighter}the terms{/effect:highlighter}
```

**Resolution:** a PHP file `PAD/subs/effects/types/<name>.php`, its style
`www/assets/effects/<name>.css` (the scripts of confetti and decipher beside them), the effect written
by `PAD/subs/effects/effect.php`: the files of `types/` are the effects, and an effect is added by
adding its files. Every named tag requires its type prefix; other namespaces do not shadow it.

---

## JSON-LD Types

### jsonld

The structured data of a page - a Schema.org object of the kind the tag names, written as one
`<script type="application/ld+json">`: the JSON-LD subsystem in `PAD/subs/jsonld/` builds it from
the fields the kind maps.

```
{jsonld:article headline=$title, author=$author, published=$date/}
{jsonld:faq data='questions'/}
{jsonld:breadcrumb/}
```

**Resolution:** a PHP file `PAD/subs/jsonld/types/<name>.php` that answers its Schema.org type and
how each of its fields maps onto it (`padJsonLdKinds()` in `PAD/lib/jsonld.php`): the files there
are the kinds, and a kind is added by adding its file. The `jsonld:` prefix is required; an unqualified name is no JSON-LD
block - article, event, person and product are the names of fields and pages.
`{jsonld:soup/}` is "there is no JSON-LD kind named 'soup'" under the strict check.

---

## Sequence Types

These types integrate with the PAD sequence subsystem.

### sequence

Accesses sequence type definitions.

```
{sequence:fibonacci/}
```

**Resolution:** `file_exists(PT . $tagName)`

**Implementation:** Delegates to `PQ/start/types/sequence.php`.

---

### action

Executes sequence actions.

```
{action:sum/}
{action:sum pull='mySequence',}
```

**Resolution:** `file_exists(PQ/actions/types/$tagName.php)`

**Implementation:** Delegates to `PQ/start/types/action.php`.

---

### pull

Retrieves values from the sequence store.

```
{pull:mySequence/}
{pull:mySequence}  ← If exists in pqStore
```

**Resolution:** `isset($pqStore[$tagName])`

**Implementation:** Delegates to `PQ/start/types/pull.php`.

---

### flag

Turns each value of a sequence into 1 or 0: whether it belongs to the named sequence type.

```
{flag:prime/}
{flag:mySeq even}
```

**Resolution:** `padTypeSeq()` - a sequence type or a stored sequence.

**Implementation:** Delegates to `PQ/start/types/flag.php`. (In an expression, `flag:name`
reads the bool store instead.)

---

### make

Creates new sequence values.

```
{make:fibonacci/}
{make:fibonacci}
```

**Resolution:** Sequence exists + make type available.

**Implementation:** Delegates to `PQ/start/types/make.php`.

---

### keep

Keeps only the values the named sequence type accepts; nothing is stored.

```
{keep:mySequence even/}
```

**Implementation:** Delegates to `PQ/start/types/keep.php`.

---

### remove

Drops the values the named sequence type accepts; no store is dropped.

```
{remove:mySequence even/}
```

**Implementation:** Delegates to `PQ/start/types/remove.php`.

---

## Type Summary Table

| Type | Source | Description |
|------|--------|-------------|
| `app` | App `_tags/` | Application-specific tags |
| `common` | apps/_common/ | Shared tags |
| `pad` | PAD/tags/ | Built-in PAD tags |
| `data` | $padDataStore | Stored data arrays |
| `content` | $padContentStore | Stored content strings |
| `bool` | $padBoolStore | Boolean flags |
| `field` | Current data | Field values |
| `property` | Parent tag | Tag properties |
| `array` | Arrays | Named arrays |
| `parm` | Enclosing tag | Parameter/option value |
| `level` | Enclosing rows | Nested array field |
| `constant` | PHP | PHP constants |
| `local` | Files | Local data files |
| `include` | APP | Include files |
| `select` | $padSelect | Declared select tables |
| `function` | PAD/functions/ | PAD functions |
| `php` | PHP | PHP functions |
| `script` | Scripts | Shell scripts |
| `sequence` | PQ | Sequence types |
| `action` | PQ | Sequence actions |
| `chart` | PAD/subs/charts/types/ | Chart kinds |
| `game` | PAD/subs/games/types/ | Games |
| `diagram` | PAD/subs/diagrams/types/ | Diagram kinds |
| `embed` | PAD/subs/embeds/types/ | Embed providers |
| `fake` | PAD/subs/fakes/types/ | Fake value kinds |
| `barcode` | PAD/subs/codes/types/ | Barcode symbologies |
| `icon` | PAD/subs/icons/types/ | Icons |
| `toy` | PAD/subs/toys/types/ | Toys |
| `widget` | PAD/subs/widgets/types/ | Widgets |
| `input` | PAD/subs/inputs/types/ | Rich form fields |
| `pattern` | PAD/subs/patterns/types/ | Seeded SVG patterns |
| `sound` | PAD/subs/sounds/types/ | Sounds |
| `printable` | PAD/subs/printables/types/ | Printables |
| `notation` | PAD/subs/notations/types/ | Notations |
| `section` | PAD/subs/sections/types/ | Landing-page sections |
| `effect` | PAD/subs/effects/types/ | Effects |
| `pull` | pqStore | Sequence values |
| `flag` | PQ | Membership flags, 1 or 0 |
| `make` | PQ | Sequence creation |
| `keep` | PQ | Filter: keep the matches |
| `remove` | PQ | Filter: drop the matches |

---

## Type Handler Files

Each type has a corresponding handler in `PAD/types/`:

```
types/
├── action.php      → Sequence action
├── app.php         → Application tag
├── array.php       → Array value
├── barcode.php     → Barcode symbology
├── bool.php        → Boolean store
├── chart.php       → Chart kind
├── common.php      → Shared _common tag
├── constant.php    → PHP constant
├── content.php     → Content store
├── data.php        → Data store
├── define.php      → Tag defined in the template ({pad:define})
├── diagram.php     → Diagram kind
├── effect.php      → Effect
├── embed.php       → Embed provider
├── fake.php        → Fake value kind
├── field.php       → Field value
├── flag.php        → Sequence membership flags
├── function.php    → PAD function
├── game.php        → Game
├── icon.php        → Icon
├── include.php     → Include file
├── input.php       → Rich form field
├── keep.php        → Sequence keep
├── level.php       → Array field of an enclosing row
├── local.php       → Local file
├── macro.php       → Macro of the template ({pad:macro})
├── make.php        → Sequence make
├── notation.php    → Notation
├── pad.php         → PAD tag
├── parm.php        → Tag parameter
├── pattern.php     → Pattern
├── php.php         → PHP function
├── printable.php   → Printable
├── property.php    → Tag property
├── pull.php        → Sequence pull
├── remove.php      → Sequence remove
├── script.php      → Shell script
├── section.php     → Section
├── select.php      → Select table query
├── sequence.php    → Sequence type
├── sound.php       → Sound
├── toy.php         → Toy
├── widget.php      → Widget
└── _go/
    ├── local.php   → Local file processing
    └── tag.php     → Tag file loading
```

---

## Examples

### Mixed Type Usage

```html
{array:users where='$active eq 1', toData='activeUsers'}

{pad:if data:activeUsers}
  <h2>Active Users ({php:count data:activeUsers/})</h2>
  {data:activeUsers}
    <div>{field:name/} - {field:email/}</div>
  {/data:activeUsers}
{pad:else}
  <p>No active users</p>
{/pad:if}

<footer>{constant:PHP_VERSION/}</footer>
```

### Explicit Type Disambiguation

```html
{-- 'date' could be a field, function, or PHP function --}
{field:date/}          ← Get date field value
{function:date 'Y-m-d'}2026-11-02{/function:date} ← Format a date
{php:date 'Y-m-d'/}    ← Use PHP date function
```

### Sequence Operations

```html
{make:fibonacci rows=10, push='fib'}{/make:fibonacci}
{action:sum pull='fib',}{$sequence}{/action:sum}
{pull:fib}{$sequence} {/pull:fib}
{remove:fib even}{$sequence} {/remove:fib}
```
