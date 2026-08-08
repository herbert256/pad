<?php

  // The order the parser works in, which is the manual's "Parse" page and had no case.
  //
  // PAD parses on demand rather than in one sweep: it looks for the first closing brace, takes
  // the opening brace nearest before it, and reads what is between them. So the innermost tag is
  // always the first one resolved, and a tag written inside another tag's parameter is finished
  // before the tag carrying it is even looked at.
  //
  // That is not an implementation detail - it is what lets a parameter be built rather than
  // written, and it is why the value has to be quoted: what the inner tag leaves behind is text
  // in the outer tag's parameter list, and unquoted text there is read as a name.

  return [

    [ 'a tag inside a parameter is resolved first',
      <<<'PAD'
      {set $myVar = 'ABC'/}
      {pad myOption='{$myVar}'}{#myOption}{/pad}
      PAD,
      'ABC' ],

    [ 'the innermost tag of a nest is the first one finished',
      <<<'PAD'
      {echo php:strtoupper(php:trim('  ab  '))}
      PAD,
      'AB' ],

    // A parameter is built this way, but a tag *name* is not: the parse order that resolves
    // {$myVar} inside a parameter does not reach a name, and {data:{$which}} leaves the level
    // without the store. The name has to be written.

    [ 'a written name reaches its store, which a built one cannot',
      <<<'PAD'
      {data 'abc'}(1,2){/data}
      {data:abc}{$abc},{/data:abc}
      PAD,
      '1,2,' ],

  ];

?>