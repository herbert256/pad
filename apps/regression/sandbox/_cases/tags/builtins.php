<?php

  // Three built-ins nothing else asks for. {foo} exists so the manual's prefix page has a
  // pad: example; {output} reloads the response mode - only 'web' is safe to name here,
  // anything else would change how this whole suite request is delivered; {action} applies
  // a named action to a stored sequence, as a tag pair.

  return [

    [ 'foo answers what the pad: prefix resolves to',
      <<<'PAD'
      {foo}
      PAD,
      'Foo tag from pad' ],

    [ 'output reloads the response mode it is already in',
      <<<'PAD'
      {output 'web'}
      PAD,
      '' ],

    [ 'action applies an action to a stored sequence',
      <<<'PAD'
      {sequence '1..4', push='caseAct'/}
      {action caseAct, reverse}{$sequence},{/action}
      PAD,
      '4,3,2,1,' ],

  ];

?>
