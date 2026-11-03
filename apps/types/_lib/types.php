<?php

  // The core tag types. Subsystem descriptions live in the subsystems application.
  // Membership is read from the engine by typesCatalog().

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

      'data'     => [ 'A data store - rows kept by {format:auto} or a file of _data/ read through the format registry.', 'stores',
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

    ];

  }

  // The groups, in the order of the indexes.

  function typesGroups () {

    return [
      'tags'      => [ 'Tags', 'Tags with code of their own - the engine, application or template' ],
      'stores'    => [ 'Stores and data', 'What a page keeps under a name and reads back' ],
      'values'    => [ 'Values of the page', 'Fields, arrays, parameters and properties where the tag stands' ],
      'sequences' => [ 'Sequences', 'Number sequences and what is done to them' ],
    ];

  }

  // Named tags no longer participate in automatic lookup. All types need a prefix.

  function typesOrder () {

    return [];

  }

?>
