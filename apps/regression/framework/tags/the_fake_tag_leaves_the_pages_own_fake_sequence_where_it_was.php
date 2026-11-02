<?php

  // The page seeds the fake sequence for values of its own; the {fake:rows} tags between - with
  // a seed of their own and without - take theirs from elsewhere and put the page's back, so
  // the next padFakeName is the one the seed gives first.

  padFakeSeed ( 42 );
  $expected = padFakeName ();
  padFakeSeed ( 42 );

?>
