# PAD Tag Types Reference

This document provides a complete reference for all PAD tag types.

## Overview

Every PAD tag has a type that determines how it's processed. Types are automatically detected based on the tag name, or can be explicitly specified using the `type:name` syntax.

## Type Syntax

### Automatic Detection

```
{tagName}
```

PAD automatically detects the type based on what `tagName` matches.

### Explicit Type

```
{type:name}
```

Explicitly specify the type for disambiguation.

**Examples:**
```
{users}              ← Auto-detected as 'select' type (if declared in $padSelect)
{select:users/}       ← Explicitly specified as select type
{field:username/}     ← Explicitly field type
{php:strtoupper 'abc'/} ← Explicitly PHP function type
```

---

## Type Detection Order

When no explicit type is given, PAD checks in this order (see `padTypeTag()` and `padTypeCommon()` in `PAD/lib/type.php`):

1. **app** - Application tag in the app's `_tags/` directories
2. **common** - Tag in the shared `_common` app
3. **pad** - Built-in PAD tag in `PAD/tags/`
4. **pull** - Sequence store value
5. **bool** - Boolean store
6. **content** - Content store
7. **select** - Declared select table
8. **data** - Data store
9. **include** - Application include file
10. **property** - Tag property
11. **field** - Field of the current data row
12. **array** - Array value
13. **parm** - Parameter value
14. **level** - Array field of an enclosing row
15. **constant** - PHP constant
16. **local** - Local data file
17. **script** - External script
18. **php** - PHP function - one that is also a pipe function (`trim`, `date`, `substr` ...) runs as the pipe function
19. **sequence** - Sequence type
20. **action** - Sequence action
21. **chart** - Chart type, a file of `PAD/subs/charts/types/`
22. **game** - Game, a file of `PAD/subs/games/types/`
23. **toy** - Toy, a file of `PAD/subs/toys/types/`
24. **diagram** - Diagram kind, a file of `PAD/subs/diagrams/types/`
25. **widget** - Widget, a file of `PAD/subs/widgets/types/`
26. **barcode** - Barcode symbology, a file of `PAD/subs/codes/types/`
27. **embed** - Embed provider, a file of `PAD/subs/embeds/types/`
28. **input** - Rich form field, a file of `PAD/subs/inputs/types/`
29. **notation** - Notation, a file of `PAD/subs/notations/types/`
30. **effect** - Effect, a file of `PAD/subs/effects/types/`
31. **function** - PAD function (fallback)

Items 21 to 30 are the subsystems of `padSubsystems` (`PAD/lib/subsystem.php`), tried in that
order. The icons (`PAD/subs/icons/types/`) and the fake kinds (`PAD/subs/fakes/types/`) are never reached
bare - their names, home or email, are those of fields and pages - only as `{icon:star/}` and
`{fake:email/}`. Nor are the patterns (`PAD/subs/patterns/types/`), the sounds
(`PAD/subs/sounds/types/`), the printables (`PAD/subs/printables/types/`) and the sections
(`PAD/subs/sections/types/`) - dots, grid, click, error, labels, letter, hero, team and footer are
the names of fields and pages too - only as `{pattern:dots/}`, `{sound:click/}`,
`{printable:labels}` and `{section:hero}`. Nor are the JSON-LD kinds (`PAD/subs/jsonld/types/`) -
article, event, person and product are the names of fields and pages - only as `{jsonld:article/}`.

A field or array that exists only as a request value - a GET, POST or cookie value the page's PHP
(its configuration, `_inits.php`, the page, `_exits.php`) does not name, held by no row - comes
after all of them: a visitor's `?prime=x` never takes the
place of the sequence, constant, `_data` file or function the template names, and is the field
only when the template means nothing else by the name.

---

## Tag Types

### app

Loads application-specific tags from `_tags/` in the page's directory or a parent's, up to the
application root.

```
{myCustomTag}
{app:myCustomTag/}
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
{if}
{pad:if}
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
{padFrame}
{common:padFrame/}
```

**Resolution:** `padCommonCheck()` - checked after `app`, before `pad`, so a common tag overrides a built-in of the same name but not an app-local one. Can be disabled per app with `$padCommon = FALSE`.

**Use case:** Tags shared by all applications - the frame of the PAD applications, `{example}`, `{demo}`, `{table}`.

---

### data

Retrieves data from the data store (`$padDataStore`).

```
{data:myData/}
{myData}  ← If myData exists in data store
```

**Resolution:** `$padDataStore[$tagName]`

**Options:**
- `print` - Output with formatting

**Example:**
```
{users toData="activeUsers"}
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
{header toContent="savedHeader"}
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
{checkAdmin toBool="isAdmin"}
{if bool:isAdmin}...{/if}
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
{users}{field:email/}{/users}
{users}{$email}{/users}  ← Equivalent
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
{users}{&current}{/users}
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

Retrieves a parameter or option of the tag directly around it - an `{if}` or `{case}` in
between is skipped, any other tag is not: inside `{users mode="fast"}{orders}` the `{orders}` is
asked, and has no `mode`.

```
{users mode="fast"}{parm:mode/}{/users}
```

**Resolution:** `padOptValue($tagName, 1)`

---

### level

Retrieves an array field of an enclosing row - the nearest level outward that holds one - and
iterates it.

```
{orders}{level:lines}{$product}{/level:lines}{/orders}
```

**Resolution:** `padGetLevelArray($tagName)`

**Use case:** Walk a nested array of the row being rendered. A `{tag $name = value}` level
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
{myfile}            ← _data/myfile.csv, the extension found for it
```

**Resolution:** `padDataFileName($tagName)` - a file directly in `_data/` (a name holds no `/`),
with its extension or with the first of `.xml`, `.json`, `.yaml`, `.csv`, `.php`, `.curl`,
`.sql` that exists; a bare tag is written without the extension.

**Supported formats:**
- `.php` - Executes PHP and returns result
- `.json` - Parsed as JSON
- `.yaml` - Parsed as YAML
- `.csv` - Parsed as CSV
- `.xml` - Parsed as XML
- `.curl` - Fetches the URL it names
- `.sql` - Runs the named query
- `.toml`, `.ini`, `.tsv`, `.ndjson`, `.md`, `.ics`, `.vcf`, `.geojson`, `.gpx`, `.vtt`, `.srt`, `.xlsx` ... - the
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
{users}  ← If 'users' is declared in $padSelect
```

**Resolution:** `isset($padSelect[$tagName])` - tables are declared in the app's `_lib/select.php` via `$padSelect` (and relations via `$padRelations`).

**Returns:** Query results as iterable data; nested select tags join automatically through `$padRelations`.

**Example:**
```
{users $id=5}
  {$username}
{/users}
```

See [DATABASE.md](../DATABASE.md) for the full Select subsystem reference.

---

### function

Executes PAD functions as tags.

```
{function:trim}content{/function:trim}
{trim}content{/trim}
```

**Resolution:** `padFunctionAsTag($tagName)`

**Behavior:**
- Supports start/end tag pairs
- Content is passed as input value
- Positional parameters become the function's arguments

**Example:**
```
{upper}hello{/upper}  →  HELLO
{replace 'a', 'b'}aaa{/replace}  →  bbb
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

A chart of the kind the tag names - the chart subsystem in `PAD/subs/charts/` draws it, as the
`{chart}` tag draws the kind its parameter names.

```
{chart:pie data='budget', label='post', value='amount'/}
{chart:bar data='sales', label='month', value='amount', stacked/}
{gauge value=72, unit='%'/}
```

**Resolution:** a file `PAD/subs/charts/types/<name>.php` (`padChartType()` in `PAD/lib/chart.php`):
the files there are the kinds, and a kind is added by adding its file. Bare, a chart kind
comes after every other kind of tag, so a tag, a store, a field or a sequence of the name wins.

---

## Game Types

### game

A little game played in the browser, of the kind the tag names - the games subsystem in
`PAD/subs/games/` sets it up, as the `{game}` tag sets up the game its parameter names.

```
{game:memory pairs=6/}
{sudoku givens=26/}
{hangman}lighthouse{/hangman}
```

**Resolution:** a PHP file `PAD/subs/games/types/<name>.php` (`padGameType()` in `PAD/lib/game.php`),
its script and style `www/assets/games/<name>.js` and `.css`: the files of `types/` are the
games, and a game is added by adding its files. Bare, a game comes after every other kind of tag, the chart kinds too.

---

## Diagram Types

### diagram

A diagram of the kind the tag names, drawn from the lines between its tags - the diagram
subsystem in `PAD/subs/diagrams/` lays it out, as the `{diagram}` tag lays out the kind its
`type=` names.

```
{diagram:flowchart direction='LR'}A[Order] --> B{Paid?}{/diagram:flowchart}
{diagram:sequence}Shop ->> Bank: Charge{/diagram:sequence}
{flowchart}A --> B{/flowchart}
```

**Resolution:** a file `PAD/subs/diagrams/types/<name>.php` (`padDiagramType()` in
`PAD/subs/diagrams/_lib/diagram.php`): the files there are the kinds, and a kind is added by adding
its file. Bare, a diagram kind comes after every other kind of tag, the chart kinds and the
games too - so `{sequence}` is the sequence tag, and the sequence diagram is
`{diagram:sequence}` or `{diagram type='sequence'}`.
## Embed Types

### embed

A video, a sound, a map, a pen or a document of the provider the tag names, behind a
click-to-load facade - the embed subsystem in `PAD/subs/embeds/` writes it, as the `{embed}` tag
writes the provider that knows its address. Named, a provider reads its own bare ids too.

```
{embed:youtube 'aqz-KE-bpKQ', title='Big Buck Bunny'/}
{vimeo '76979871', title='The new Vimeo player'/}
{openstreetmap '52.3731,4.8922', zoom=15, title='Dam Square'/}
```

**Resolution:** a file `PAD/subs/embeds/types/<name>.php` (`padEmbedProviders()` in `PAD/lib/embed.php`):
the files there are the providers, and a provider is added by adding its file. Bare, a provider
comes after every other kind of tag, the chart kinds and the games too, so a tag, a store, a
field or a sequence of the name wins. `{embed:zigzag/}` is "there is no embed provider named
'zigzag'".

---

## Fake Types

### fake

A fake value of the kind the tag names - the fake subsystem in `PAD/subs/fakes/` makes it, as the
`{fake}` tag makes the kind its parameter names.

```
{fake:email/}
{fake:number from=1, to=6/}
{fake:date from='2026-01-01', to='2026-12-31', format='j M Y'/}
```

**Resolution:** a file `PAD/subs/fakes/types/<name>.php` (`padFakeKinds()` in `PAD/lib/fake.php`): the
files there are the kinds, and a kind is added by adding its file. Never bare: `{email/}` is no
fake. `{fake:zebra/}` is "there is no fake kind named 'zebra'".
## Barcode Types

### barcode

A barcode of the symbology the tag names - the codes subsystem in `PAD/subs/codes/` encodes and
draws it, as the `{barcode}` tag draws the symbology its `type=` names.

```
{barcode:ean13 '400638133393'/}
{barcode:datamatrix $serial, size=120/}
{itf14 '1540014128876'/}
```

**Resolution:** a file `PAD/subs/codes/types/<name>.php` (`padKinds ('codes')`): the files there are
the symbologies - `qr` among them, which the `{qr}` tag writes by a name of its own - and a
symbology is added by adding its file and its encoder in `PAD/subs/codes/_lib/`. Bare, a symbology
comes after every other kind of tag, the chart kinds and the games too.

---

## Icon Types

### icon

An icon of PAD's own set, the one the tag names - the icon subsystem in `PAD/subs/icons/` draws it,
as the `{icon}` tag draws the icon its parameter names.

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
subsystem in `PAD/subs/patterns/` draws it, as the `{pattern}` tag draws the kind its parameter
names, the same bytes for the same options.

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
subsystem in `PAD/subs/toys/` sets it up, as the `{toy}` tag sets up the toy its parameter names.

```
{toy:life pattern='gun'/}
{mandelbrot zoom=40, center='-0.745, 0.11'/}
{lsystem 'dragon', iterations=14/}
```

**Resolution:** a PHP file `PAD/subs/toys/types/<name>.php` (`padToyType()` in `PAD/lib/toy.php`), its
script `www/assets/toys/<name>.js` and its style, when it has one, `<name>.css` beside it: the files
of `types/` are the toys, and a toy is added by adding its files. Bare, a toy comes after every other
kind of tag, the chart kinds and the games too.

## Widget Types

### widget

An interactive part of the page - tabs, an accordion, a carousel, a modal, a copy box, a poll
... - of the kind the tag names: the widgets subsystem in `PAD/subs/widgets/` builds it, as the
`{widget}` tag builds the widget its first parameter names.

```
{widget:tabs active=2}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/widget:tabs}
{widget:modal 'terms', title='Terms of sale'}...{/widget:modal}
{tabs}{tab 'Overview'}...{/tab}{/tabs}
```

**Resolution:** a PHP file `PAD/subs/widgets/types/<name>.php` (`padWidgetType()` in `PAD/lib/widget.php`),
the widget's tag code; its functions in `PAD/subs/widgets/_lib/<name>.php`, its style and script in
`www/assets/widgets/<name>.css` and `.js`. The files of `types/` are the widgets, and a widget is
added by adding its files. Bare, a widget comes after every other kind of tag - the chart kinds
and the games too - so a store, a field or an array of the name wins: a page's `$tabs` array
is the data of `{tabs}`. `{copy}` stays a built-in tag that runs the copy widget, as PHP has a
`copy()` function that a bare name reaches first; `{tab}`, the item of `{tabs}`, `{accordion}`
and `{carousel}`, is a built-in tag.

---

## Input Types

### input

A rich form field of the kind the tag names - a slider, a stepper, a date picker, stars, a
signature ... - the inputs subsystem in `PAD/subs/inputs/` writes it, a field of its `{form}` as
`{input}` is: a native field a form posts without any script, made more of by its script.

```
{input:slider 'volume', label='Volume', unit='%', rules='integer|between:10,90'/}
{input:datepicker 'arrival', label='Arrival', min='2026-01-01'/}
{stars 'score', label='Score'/}
```

**Resolution:** a PHP file `PAD/subs/inputs/types/<name>.php`, its script and style in
`www/assets/inputs/`, the field written by `PAD/subs/inputs/input.php`: the files of `types/` are
the kinds, and a kind is added by adding its file. Its items are read raw, as `{input}`'s are -
`label=`, `rules=`, `min=` are the field's, never an option of the engine. Bare, an input kind
comes last of the subsystems, so a store, a field, an array of the name - a page's `$slider` -
or a kind of another subsystem wins; write `input:slider` there.

## Sound Types

### sound

A short sound made from a recipe, of the kind the tag names - the sounds subsystem in
`PAD/subs/sounds/` writes its button or its binding, as the `{sound}` tag does for the sound its
parameter names, and the browser renders and plays it.

```
{sound:chime/}
{sound:success on='click', target='#save'/}
{sound:bell download/}
```

**Resolution:** a PHP file `PAD/subs/sounds/types/<name>.php` that returns the recipe
(`padSoundKinds()` in `PAD/lib/sound.php`): the files there are the sounds, and a sound is added by
adding its file. Never bare: `{chime/}` is no sound - click, error and pop are the names of fields
and pages. `{sound:zigzag/}` is "there is no sound named 'zigzag'".

## Printable Types

### printable

Pages made for paper, of the kind the tag names - sheets of labels, badges, tickets, a letter -
laid out by the printables subsystem in `PAD/subs/printables/`, as the `{printable}` tag lays out
the kind its parameter names: the content between the tags one item, repeated over the data.

```
{printable:labels data='products', sheet='a4-3x8'}<b>{$name}</b>{/printable:labels}
{printable:tickets data='seats'}<h2>{$show}</h2><aside>{qr $code/}</aside>{/printable:tickets}
```

**Resolution:** a PHP file `PAD/subs/printables/types/<name>.php` that answers its plan
(`padPrintableKinds()` in `PAD/lib/printable.php`): the files there are the kinds, and a kind is
added by adding its file. Never bare: `{labels}` is no printable - labels, cards and letter are the
names of fields and pages. `{printable:zigzag}...{/printable:zigzag}` is "there is no printable
named 'zigzag'".

## Notation Types

### notation

A text written in a notation and drawn on the server - a formula in AsciiMath, a chemical
equation, a tune in ABC, a guitar chord, piano keys, braille, morse, semaphore, signal flags, a
chess, go or draughts position - of the kind the tag names: the notations subsystem in
`PAD/subs/notations/` draws it, as the `{notation}` tag draws the kind its first parameter names.
The text is the first parameter, or the content between the tags taken as it stands.

```
{notation:math}x = (-b +- sqrt(b^2 - 4ac))/(2a){/notation:math}
{notation:chord 'Am', instrument='ukulele'/}
{braille 'Hello'/}
{chess $fen/}
```

**Resolution:** a PHP file `PAD/subs/notations/types/<name>.php`, its functions in
`PAD/subs/notations/_lib/<name>.php` and its style in `www/assets/notations/<name>.css`: the files
of `types/` are the notations, and a notation is added by adding its files. Bare, a notation
comes after every other kind of tag and the subsystems before it in `padSubsystems`, so a store, a
field, an array of the name or a kind of another subsystem wins: bare `{chord}` is the chord
diagram of the charts, the guitar chord `{notation:chord}`. `{chess}` is the built-in tag that
draws the chess notation, before any field or store of the name, as it did. `{notation:zebra/}` is "there is no notation named 'zebra'".

---

## Section Types

### section

A block of a landing page - a hero, the features, the prices, the questions, a footer ... - of the
kind the tag names: the sections subsystem in `PAD/subs/sections/` builds it, as the `{section}` tag
builds the section its first parameter names.

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
`PAD/subs/effects/` writes it, as the `{effect}` tag writes the effect its first parameter names.

```
{effect:typewriter}Hello, world{/effect:typewriter}
{effect:countup to=1250, suffix='+'/}
{highlighter}the terms{/highlighter}
```

**Resolution:** a PHP file `PAD/subs/effects/types/<name>.php`, its style
`www/assets/effects/<name>.css` (confetti's script `confetti.js` beside it), the effect written
by `PAD/subs/effects/effect.php`: the files of `types/` are the effects, and an effect is added by
adding its files. Bare, an effect comes after every other kind of tag and after the subsystems
before it in `padSubsystems`, so a store, a field or an array of the name wins.

---

## JSON-LD Types

### jsonld

The structured data of a page - a Schema.org object of the kind the tag names, written as one
`<script type="application/ld+json">`: the JSON-LD subsystem in `PAD/subs/jsonld/` builds it from
the fields the kind maps, as the `{jsonld}` tag builds the kind its first parameter names.

```
{jsonld:article headline=$title, author=$author, published=$date/}
{jsonld:faq data='questions'/}
{jsonld:breadcrumb/}
```

**Resolution:** a PHP file `PAD/subs/jsonld/types/<name>.php` that answers its Schema.org type and
how each of its fields maps onto it (`padJsonLdKinds()` in `PAD/lib/jsonld.php`): the files there
are the kinds, and a kind is added by adding its file. Never bare: `{article/}` is no JSON-LD
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
{mySequence:sum}
```

**Resolution:** `file_exists(PQ/actions/types/$tagName.php)`

**Implementation:** Delegates to `PQ/start/types/action.php`.

---

### pull

Retrieves values from the sequence store.

```
{pull:mySequence/}
{mySequence}  ← If exists in pqStore
```

**Resolution:** `isset($pqStore[$tagName])`

**Implementation:** Delegates to `PQ/start/types/pull.php`.

---

### flag

Turns each value of a sequence into 1 or 0: whether it belongs to the named sequence type.

```
{flag:prime/}
{mySeq:flag even}
```

**Resolution:** `padTypeSeq()` - a sequence type or a stored sequence.

**Implementation:** Delegates to `PQ/start/types/flag.php`. (In an expression, `flag:name`
reads the bool store instead.)

---

### make

Creates new sequence values.

```
{make:fibonacci/}
{fibonacci:make}
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
├── define.php      → Tag defined in the template ({define})
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
├── macro.php       → Macro of the template ({macro})
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
{users where='$active eq 1', toData='activeUsers'}

{if data:activeUsers}
  <h2>Active Users ({php:count data:activeUsers/})</h2>
  {data:activeUsers}
    <div>{field:name/} - {field:email/}</div>
  {/data:activeUsers}
{else}
  <p>No active users</p>
{/if}

<footer>{constant:PHP_VERSION/}</footer>
```

### Explicit Type Disambiguation

```html
{-- 'date' could be a field, function, or PHP function --}
{field:date/}          ← Get date field value
{function:date}       ← Use PAD date function
{php:date 'Y-m-d'/}    ← Use PHP date function
```

### Sequence Operations

```html
{fibonacci:make rows=10, push='fib'}{/fibonacci:make}
{fib:sum}{$sequence}{/fib:sum}
{pull:fib}{$sequence} {/pull:fib}
{fib:remove even}{$sequence} {/fib:remove}
```
