<?php

  // Handles the rows option when it stands on its own: falls back to the first page by
  // including handling/types/page.php.
  //
  // Does nothing when page, start or end is present as well - those options run their own
  // handler, and each of them already takes rows into account.

  if ( ! isset ( $padPrm [$pad] ['page'] ) )
    if ( ! isset ( $padPrm [$pad] ['start'] ) )
      if ( ! isset ( $padPrm [$pad] ['end'] ) )
        include PAD . 'handling/types/page.php';

?>