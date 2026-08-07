<?php

  // A pipe function written as a tag pair, which applies it to everything between the two halves
  // instead of to a piped value. types/function.php is the handler, and padTypeFunction() decides
  // which kind of function the name is - one the application supplies in _functions/, or a
  // built-in - exactly as it does in a pipe.
  //
  // Every one of these rendered its answer followed by the source it was worked out from -
  // {upper}ab{/upper} came out ABab - because the handler returned the result without clearing
  // the content it had just consumed, so the level emitted both. tags/code.php and
  // tags/sandbox.php had the same fault.

  return [

    [ 'a built-in function as a tag',
      <<<'PAD'
      {upper}ab{/upper}
      PAD,
      'AB' ],

    [ 'a function the application supplies',
      <<<'PAD'
      {exclaim}ab{/exclaim}
      PAD,
      'ab!' ],

    [ 'the function: prefix says to read the name as one',
      <<<'PAD'
      {function:upper}ab{/function:upper}
      PAD,
      'AB' ],

    [ 'and the pipe spelling of the same thing agrees',
      <<<'PAD'
      {echo 'ab' | upper}
      PAD,
      'AB' ],

  ];

?>