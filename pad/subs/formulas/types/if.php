<?php

  // if - IF(test, [then], [else]) is then when the test holds and else when it does not - FALSE
  // when else is left out, 0 when the one chosen is left empty, IF(A1,,1). A test is a logical
  // value, a number (0 is FALSE) or the text TRUE or FALSE; another text is #VALUE!, an error the
  // answer.
  //
  //   =IF(5>3, "yes", "no")         yes
  //   =IF(1>2, "yes")               FALSE
  //   =IF("maybe", 1, 2)            #VALUE!

  return [
    'group' => 'Logic',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $test = padFormulaArgBool ( $args [0], $env );
      if ( padFormulaIsError ( $test ) )
        return $test;
      if ( $test )
        return $args [1];
      return array_key_exists ( 2, $args ) ? $args [2] : FALSE;
    }
  ];

?>
