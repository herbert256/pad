<?php

  // array OP array.
  //
  // Equality compares the two arrays whole. The ordering operators use PHP's own array order:
  // the array with fewer elements is the smaller, and two of one size are compared element by
  // element. The logical operators read an array's truth the way singleArr.php does, an empty
  // array being false and any populated one true. All of them yield PAD's string booleans.
  //
  // Concatenation and arithmetic need one value a side, so padEvalReduce() makes one of each
  // array - the sum of its elements where they are all numbers, and otherwise its leaves run
  // together - and doubleVarVar.php does the work from there.

  if     ( $opr == 'EQ'  ) $now = ($left ==  $right) ? 1 : '';
  elseif ( $opr == 'NE'  ) $now = ($left !=  $right) ? 1 : '';
  elseif ( $opr == 'LT'  ) $now = ($left <   $right) ? 1 : '';
  elseif ( $opr == 'LE'  ) $now = ($left <=  $right) ? 1 : '';
  elseif ( $opr == 'GT'  ) $now = ($left >   $right) ? 1 : '';
  elseif ( $opr == 'GE'  ) $now = ($left >=  $right) ? 1 : '';

  elseif ( $opr == 'AND' ) $now = ( count ($left) and count ($right) ) ? 1 : '';
  elseif ( $opr == 'OR'  ) $now = ( count ($left) or  count ($right) ) ? 1 : '';
  elseif ( $opr == 'XOR' ) $now = ( count ($left) xor count ($right) ) ? 1 : '';

  else {

    $left  = padEvalReduce ( $left  );
    $right = padEvalReduce ( $right );

    include PAD . 'eval/go/doubleVarVar.php';

  }

?>
