<?php

  // Build build for range: returns the whole list in one go, the type behind {sequence
  // '1..10'} and the other explicit ranges, letters included.
  //
  // Falls back to the tag's first parameter $padParm when no range= was given, and hands it
  // to padGetRange(), which reads 'a..b', a bare 'b' as 1..b and nothing at all as 1..10,
  // stepping by $pqInc as it goes. Marking increment as done keeps the fixed iterator from
  // applying that step a second time. A range='0' is a range, the bare 0 of 1..0: tested
  // for truth it was no range at all, and both it and padGetRange's own test made it 1..10.

  // A bare range - {sequence range}, or sequence:range(n), which hands the TRUE of a bare
  // option over - is no range given either: taken as the text 1, it was the range 1..1. An
  // expression has no tag parameter to fall back to.

  if ( $pqParm === NULL or $pqParm === FALSE or $pqParm === '' or $pqParm === TRUE )
    $pqParm = $padParm ?? '';

  $pqDone [] = 'increment';

  return padGetRange ( $pqParm,  $pqInc );

?>
