<?php

  // The unary operator, in both its spellings.
  //
  // eval/go/singleVar.php inverts a scalar and eval/go/singleArr.php an array, an empty array
  // being the false one. Which action file carries the operand there depends on the shape:
  // eval/actions/single.php when a value follows it in a longer expression, doubleLeft.php when
  // the operator opens a pipe segment of its own, and alone.php when it stands by itself and the
  // piped value is its operand.
  //
  // The word form is here because it did not work. padEval_one listed it as 'not' while the
  // tokeniser stores word operators uppercase, so NOT never matched as unary: it fell through to
  // the binary path, where doubleVarVar.php has no line for it either, and {echo '' | not 0}
  // rendered 200.
  //
  // eval/actions/singleRight.php has no case and cannot have one. It is meant for a unary
  // operator whose right-hand neighbour is another operator, but padEvalDouble() collapses every
  // adjacent operator pair before the precedence walk begins, so that shape never survives to be
  // seen - {echo '' | ! + 1} and every variant of it goes through alone.php instead.

  return [

    [ '! inverts a false value',
      <<<'PAD'
      {if ! 0 eq 1}
        y
      {/if}
      PAD,
      'y' ],

    [ '! inverts a true value',
      <<<'PAD'
      {if ! 1 eq ''}
        y
      {/if}
      PAD,
      'y' ],

    [ 'an empty array is the false one',
      <<<'PAD'
      {echo '' | ! []}
      PAD,
      '1' ],

    [ 'a populated array is the true one',
      <<<'PAD'
      {echo '' | ! [1] . 'z'}
      PAD,
      'z' ],

    [ 'not is the same operator',
      <<<'PAD'
      {if not 0}
        y
      {/if}
      {if not 1}
        n
      {/if}
      PAD,
      'y' ],

    [ 'not over an array',
      <<<'PAD'
      {echo '' | not []}
      PAD,
      '1' ],

    [ 'not standing alone takes the piped value',
      <<<'PAD'
      {echo 0 | not}
      PAD,
      '1' ],

    [ 'and gives nothing for a true one',
      <<<'PAD'
      {echo 5 | not}
      PAD,
      '' ],

  ];

?>