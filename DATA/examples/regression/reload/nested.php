<?php

  // A file saved below a subdirectory holding a page index - shelf/index.php with
  // shelf/index.pad, the page ?shelf - moves the stamp the poll answers: every directory
  // with an index.php was passed over as a nested application's, so a save below one never
  // reloaded a page. The template is saved one second past the stamp, which must follow.

  $nestedPoll   = $padHost . 'regression/reload/?sample&padReload';
  $nestedBefore = trim ( padCurl ( $nestedPoll ) ['data'] ?? '' );

  if ( ctype_digit ( $nestedBefore ) ) {
    touch ( APP . 'shelf/index.pad', (int) $nestedBefore + 1 );
    clearstatcache ();
  }

  $nestedAfter = trim ( padCurl ( $nestedPoll ) ['data'] ?? '' );

  $nestedVerdict = ( ctype_digit ( $nestedBefore ) and ctype_digit ( $nestedAfter )
                     and (int) $nestedAfter > (int) $nestedBefore ) ? 'yes' : 'NO';

?>
