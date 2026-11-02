<?php

  // What the types application says of each type of tag, the prefix before the colon of
  // {pad:if}: [ its one line, its group, its syntax - a form a line ]. The types themselves are
  // the handlers of pad/types/, as the engine has them (typesCatalog, _lib/catalog.php): a type
  // added there and not here still has its page, its line read from the comment of its handler.

  function typesInfo () {

    return [

      'pad'      => [ 'A built-in tag of the engine - a file of pad/tags/.', 'tags',
                      "{pad:if \$count gt 0}...{/pad:if}" ],
      'app'      => [ 'A tag of the application, a file of a _tags/ directory from the page up to the root.', 'tags',
                      "{app:greeting name='Ann'/}" ],
      'common'   => [ 'A tag or a snippet of the shared _common application.', 'tags',
                      "{common:demo}...{/common:demo}\n{common:padFrame}...{/common:padFrame}" ],
      'define'   => [ 'A tag defined in the template by {pad:define} - its body the template of the tag.', 'tags',
                      "{pad:define 'hello'}Hello {#name}{/pad:define}\n{define:hello name='Ann'/}" ],
      'macro'    => [ 'A tag defined in the template by {pad:macro} - its body rendered once with the options as its fields.', 'tags',
                      "{pad:macro 'card', title}<b>{\$title}</b>{/pad:macro}\n{macro:card title='One'/}" ],
      'include'  => [ 'A snippet of the application, a file of an _include/ directory.', 'tags',
                      "{include:card/}" ],
      'function' => [ 'A pipe function used as a tag, over the content between its tags.', 'tags',
                      "{function:upper}shout{/function:upper}" ],
      'php'      => [ 'A PHP function called as a tag, its parameters the function\'s arguments.', 'tags',
                      "{php:strtoupper 'abc'/}\n{pad:echo php:strtoupper('abc')/}" ],
      'script'   => [ 'A shell script of a _scripts/ directory, its output the tag\'s.', 'tags',
                      "{script:backup/}" ],

      'data'     => [ 'A data store - rows kept by {pad:data} or a file of _data/ read through the format registry.', 'stores',
                      "{data:colors}{\$colors} {/data:colors}" ],
      'content'  => [ 'A content store - a piece of template kept by {pad:content} to be shown elsewhere.', 'stores',
                      "{content:footer/}" ],
      'bool'     => [ 'A boolean store - a flag set by {pad:bool}, its pair showing when it is true.', 'stores',
                      "{bool:isAdmin}...@else@...{/bool:isAdmin}\n{bool:isAdmin}...{/bool:isAdmin}" ],
      'select'   => [ 'A table declared in $padSelect - its rows read from the database, joins and all.', 'stores',
                      "{select:users}{\$name}{/select:users}" ],
      'local'    => [ 'A data file of a _data/ directory, read by its file name.', 'stores',
                      "{local:menu.json}{\$label}{/local:menu.json}" ],
      'pull'     => [ 'A stored sequence - the terms a {sequence:*} pushed to the store under a name.', 'stores',
                      "{pull:nums}{\$sequence} {/pull:nums}" ],

      'field'    => [ 'A field of the current row, or a variable of the page.', 'values',
                      "{field:name/}\n{\$name}" ],
      'array'    => [ 'An array of the current row or of the page, its elements the rows of the tag.', 'values',
                      "{array:items}{\$items} {/array:items}" ],
      'level'    => [ 'An array field of a row of an enclosing tag, its elements the rows of the tag.', 'values',
                      "{level:kids}{\$name}{/level:kids}" ],
      'parm'     => [ 'A parameter or option of the tag around it, read by its name.', 'values',
                      "{parm:label/}\n{#label}" ],
      'property' => [ 'A property of the tag around it - its count, its current row, whether it is the first.', 'values',
                      "{property:count/}\n{&count}" ],
      'constant' => [ 'A PHP constant, by its name.', 'values',
                      "{constant:PHP_INT_SIZE/}" ],

      'sequence' => [ 'A sequence type of the sequence subsystem, its terms the rows of the tag.', 'sequences',
                      "{sequence:fibonacci rows=10}{\$fibonacci} {/sequence:fibonacci}\nsequence:fibonacci(8)" ],
      'action'   => [ 'A sequence action - reverse, sort, sum, first ... - over the terms it is given.', 'sequences',
                      "{action:reverse '1..5'}{\$sequence} {/action:reverse}\n{pull:nums reverse}{\$sequence} {/pull:nums}" ],
      'flag'     => [ 'Every term a 1 or a 0: whether it belongs to the sequence type named.', 'sequences',
                      "{flag:prime rows=8}{\$sequence} {/flag:prime}" ],
      'make'     => [ 'The terms of a sequence type, made as a play - the same terms {sequence:x} gives.', 'sequences',
                      "{make:fibonacci rows=8}{\$sequence} {/make:fibonacci}" ],
      'keep'     => [ 'The terms that belong to the sequence type named, the others left out.', 'sequences',
                      "{keep:prime rows=5, from=10}{\$sequence} {/keep:prime}" ],
      'remove'   => [ 'The terms that do not belong to the sequence type named.', 'sequences',
                      "{remove:prime rows=6}{\$sequence} {/remove:prime}" ],

      'avatar' => [ 'Deterministic local SVG identity artwork.', 'subsystems', "{avatar:rings 'identity', label='Name'/}" ],
      'chart'    => [ 'A chart type, a file of pad/subs/charts/types/, drawn as inline SVG.', 'subsystems',
                      "{chart:pie data='rows', label='name', value='amount'/}\n{chart:pie data='rows', ...}\n{chart:pie data='rows', .../}" ],
      'game'     => [ 'A game, a file of pad/subs/games/types/, played in the browser.', 'subsystems',
                      "{game:memory pairs=6/}\n{game:memory/}\n{game:memory/}" ],
      'toy'      => [ 'A toy, a file of pad/subs/toys/types/ - a live simulation in the browser.', 'subsystems',
                      "{toy:life pattern='glider'/}\n{toy:life/}\n{toy:life/}" ],
      'diagram'  => [ 'A diagram type, a file of pad/subs/diagrams/types/, laid out on the server.', 'subsystems',
                      "{diagram:state}...{/diagram:state}\n{diagram:state}...{/diagram:state}" ],
      'widget'   => [ 'A widget, a file of pad/subs/widgets/types/, that works without JavaScript.', 'subsystems',
                      "{widget:tabs}{pad:tab 'One'}...{/pad:tab}{/widget:tabs}\n{widget:tabs}...{/widget:tabs}\n{widget:tabs}...{/widget:tabs}" ],
      'barcode'  => [ 'A barcode symbology, a file of pad/subs/codes/types/, encoded on the server.', 'subsystems',
                      "{barcode:ean13 '400638133393'/}\n{barcode:ean13 '400638133393'/}" ],
      'embed'    => [ 'An embed provider, a file of pad/subs/embeds/types/ - loaded only on a click.', 'subsystems',
                      "{embed:youtube 'aqz-KE-bpKQ', title='Big Buck Bunny'/}\n{embed:auto 'https://youtu.be/aqz-KE-bpKQ'/}\n{embed:youtube 'aqz-KE-bpKQ'/}" ],
      'input'    => [ 'A rich form field, a file of pad/subs/inputs/types/, posted by any form.', 'subsystems',
                      "{input:slider 'volume', label='Volume'/}" ],
      'icon'     => [ 'An icon, a file of pad/subs/icons/types/, drawn as inline SVG - never bare.', 'subsystems',
                      "{icon:star size=24/}\n{icon:star size=24/}" ],
      'fake'     => [ 'A fake kind, a file of pad/subs/fakes/types/ - the same values for the same seed, never bare.', 'subsystems',
                      "{fake:email/}\n{fake:email/}\n{fake:rows 'name, email', rows=3}...{/fake:rows}" ],
      'pattern'  => [ 'A pattern, a file of pad/subs/patterns/types/ - a seeded SVG background, never bare.', 'subsystems',
                      "{pattern:topography seed=7/}\n{pattern:topography seed=7/}" ],
      'sound'    => [ 'A sound, a file of pad/subs/sounds/types/ - a recipe the browser plays and the server writes as a WAV, never bare.', 'subsystems',
                      "{sound:chime/}\n{sound:chime/}\n{sound:pop on='click', target='#save'/}" ],
      'printable' => [ 'A printable, a file of pad/subs/printables/types/ - pages made for paper, the same on the screen, printed and as a PDF, never bare.', 'subsystems',
                      "{printable:labels data='rows', sheet='a4-3x8'}...{/printable:labels}\n{printable:labels data='rows'}...{/printable:labels}" ],
      'notation' => [ 'A notation, a file of pad/subs/notations/types/ - a formula, a tune, a word or a position drawn on the server.', 'subsystems',
                      "{notation:braille 'Hello'/}\n{notation:braille 'Hello'/}\n{notation:math}a/b{/notation:math}" ],
      'section'  => [ 'A section of a landing page, a file of pad/subs/sections/types/ - pure CSS, coloured by the theme, never bare.', 'subsystems',
                      "{section:hero title='Build faster'}...{/section:hero}\n{section:hero title='Build faster'}...{/section:hero}" ],
      'effect'   => [ 'An effect, a file of pad/subs/effects/types/ - motion and decoration, CSS first, still under reduced motion.', 'subsystems',
                      "{effect:typewriter}Hello{/effect:typewriter}\n{effect:typewriter}Hello{/effect:typewriter}" ],
      'jsonld'   => [ 'A JSON-LD kind, a file of pad/subs/jsonld/types/ - the structured data of a page as a Schema.org object, never bare.', 'subsystems',
                      "{jsonld:article headline=\$title, author=\$author/}\n{jsonld:article data='post'/}\n{jsonld:faq data='questions'/}" ],

    ];

  }

  // The groups, in the order of the indexes.

  function typesGroups () {

    return [
      'tags'       => [ 'Tags',               'Tags with code of their own - the engine\'s, the application\'s, the template\'s' ],
      'stores'     => [ 'Stores and data',    'What a page keeps under a name and reads back'                                    ],
      'values'     => [ 'Values of the page', 'Fields, arrays, parameters and properties where the tag stands'                   ],
      'sequences'  => [ 'Sequences',          'Number sequences and what is done to them'                                        ],
      'subsystems' => [ 'Subsystems',         'A kind per file of a directory of the engine'                                     ],
      'kinds'      => [ 'Options and helpers', 'Subsystem kinds selected by parameters, options, helper calls or spreadsheet formulas' ],
    ];

  }

  // Named tags no longer participate in automatic lookup. All types need a prefix.

  function typesOrder () {

    return [];

  }

?>
