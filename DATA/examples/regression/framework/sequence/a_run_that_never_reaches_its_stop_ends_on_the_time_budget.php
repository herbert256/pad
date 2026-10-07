<?php

  // A run whose stop= it never reaches - and=3 gives 0 to 3 for ever, stop=50 is never
  // met - ends on $padSeqMaxSeconds, a fifth of a second here, with the terms it found,
  // fewer than the cap's million: it went on to $padSeqMaxTries, a million candidates,
  // which for a costly test is far past PHP's 30-second limit, and the page died with a 500
  // instead of ending on its cap. The terms are counted as made, none rendered.

  $padSeqMaxSeconds = 0.2;

  $budgetFound  = count ( pqTerms ( 'and', 3, [ 'stop' => 50 ] ) );
  $budgetResult = ( $budgetFound > 0 and $budgetFound < 1000000 ) ? 'ended before the cap' : "ended after $budgetFound terms";

?>
