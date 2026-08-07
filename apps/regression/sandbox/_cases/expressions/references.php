<?php

  // The typed references, read from inside an expression rather than written as a tag.
  //
  // The prefixes group covers the tag spellings; these are the other half, one file each in
  // eval/single/, reached through eval/type/single.php. They are not the same code and had not
  // been exercised at all - which is how object: came to be reading the evaluator's own local
  // variables rather than the page's, and how include: and local: sat returning the literal
  // string 'todo'.
  //
  // Each case joins the reference to something, because that is what proves a value came back:
  // a reference that resolved to nothing would leave the same output as one that failed.

  return [

    [ 'field: reads a variable',
      <<<'PAD'
      {set $x = 'v'/}
      {echo '' | field:x . '!'}
      PAD,
      'v!' ],

    [ 'data: reads the data store',
      <<<'PAD'
      {data 'd'}
        [1,2]
      {/data}
      {echo '' | data:d . data:d}
      PAD,
      '1212' ],

    [ 'content: reads the content store',
      <<<'PAD'
      {content 'k'}
        hi
      {/content}
      {echo '' | content:k . '!'}
      PAD,
      'hi!' ],

    [ 'flag: reads the boolean store',
      <<<'PAD'
      {bool 'b'}
        1
      {/bool}
      {echo '' | flag:b . '!'}
      PAD,
      '1!' ],

    [ 'pull: reads a stored sequence',
      <<<'PAD'
      {sequence '1..3', push='p'/}
      {echo '' | pull:p . pull:p}
      PAD,
      '66' ],

    // array: and level: both want a field that holds an array rather than a value, which is why
    // the data here is nested. array: looks the name up as a field and insists on an array;
    // level: walks outwards through the enclosing levels until one carries an array under it.
    // Reduced against each other the three-element array sums to 3 twice over.

    [ 'array: insists on an array',
      <<<'PAD'
      {data 'r'}
        [{"sub":[1,2]}]
      {/data}
      {r}
        {echo '' | array:sub . array:sub}
      {/r}
      PAD,
      '33' ],

    [ 'level: finds the array at an enclosing level',
      <<<'PAD'
      {data 'r'}
        [{"sub":[1,2]}]
      {/data}
      {r}
        {echo '' | level:sub . level:sub}
      {/r}
      PAD,
      '33' ],

    // object: reads a php variable the page left behind. It used to be written as a variable
    // variable, and since this file is included from inside padEvalType() that reached the
    // evaluator's own locals instead of the page's - object:myself handed back the piped value,
    // and no name a page could define resolved at all.
    //
    // $objFixture comes from _lib/cases.php and the case renders in that scope, because object:
    // reads $GLOBALS and a sandboxed pass has the application globals taken out of it. The usual
    // fourth-element setup would therefore be invisible to this one reference.

    [ 'object: reads a php variable',
      <<<'PAD'
      {echo '' | object:objFixture . object:objFixture}
      PAD,
      'abab',
      'scope' ],

    [ 'parm: reads the parameter of the tag it stands in',
      <<<'PAD'
      {withexpr label='hi'/}
      PAD,
      'hi!' ],

    [ 'constant: reads a php constant',
      <<<'PAD'
      {echo '' | constant:PHP_INT_SIZE + 0}
      PAD,
      '8' ],

    [ 'property: reads an iteration value',
      <<<'PAD'
      {data 'xs'}
        [7,8]
      {/data}
      {xs}
        {echo '' | property:current . ','}
      {/xs}
      PAD,
      '1,2,' ],

  ];

?>