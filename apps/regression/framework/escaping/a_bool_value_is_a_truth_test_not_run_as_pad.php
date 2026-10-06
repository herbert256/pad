<?php

  // {bool} from a value is that value's own truth, not the value run as PAD: $v names a
  // sequence tag and '1 eq 2' is an expression - run, the first would make a sequence and
  // the second would be FALSE - so a value kept its text and both are TRUE; '0' is FALSE.
  // A written-out literal second parameter is still the expression it is: 'false' is FALSE.

  $v = 'sequence:fibonacci(6)';
  $x = '1 eq 2';
  $w = '0';

?>
