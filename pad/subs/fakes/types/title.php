<?php

  // title - A heading of lorem ipsum, its first letter a capital and no full stop after it:
  // words= words, else two to five - Magna aliqua consequat - for the title of a post, a page
  // or a product in a prototype's list. A slug in the same row is made from it.

  $words = padFakeOpt ( 'words' );

  return ucfirst ( padFakeWords ( $words === NULL ? padFakeNumber ( 2, 5 ) : max ( 1, (int) $words ) ) );

?>
