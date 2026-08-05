<?php

  // Build build for range: returns the whole list in one go, the type behind {sequence
  // '1..10'} and the other explicit ranges, letters included.
  //
  // Falls back to the tag's first parameter $padParm when no range= was given, and hands it
  // to padGetRange(), which reads 'a..b', a bare 'b' as 1..b and nothing at all as 1..10,
  // stepping by $pqInc as it goes. Marking increment as done keeps the fixed iterator from
  // applying that step a second time.

  if ( ! $pqParm )
    $pqParm = $padParm;

  $pqDone [] = 'increment';

  return padGetRange ( $pqParm,  $pqInc );

?>