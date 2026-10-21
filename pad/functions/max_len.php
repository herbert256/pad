<?php

  // Pipe function max_len(n): truncates the value to at most n characters and leaves shorter
  // values untouched. A plain cut - no ellipsis, no respect for word boundaries - counted in
  // characters, so a multibyte letter is never cut in half. The count is read as a number,
  // as substr's are: an empty one was a TypeError out of mb_substr. A negative count allows
  // no characters, as left's does - mb_substr read it as a length from the end and
  // max_len(-1) cut only the last character off.

  $padMaxLen = max ( 0, (int) $parm [0] );

  include_once PAD . 'functions/_lib/grapheme.php';

  if ( padGraphemeLength ( $value ) > $padMaxLen )
    return padGraphemeCut ( $value, 0, $padMaxLen );
  else
    return $value;

?>
