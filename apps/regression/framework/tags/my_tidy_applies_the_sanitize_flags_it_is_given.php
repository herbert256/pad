<?php

  // The sanitize step of PAD's own tidy pass does what its flags name: STRIP_LOW takes the
  // tab out before the tab-to-space step sees it. The flags were cast to int, 0 each, so
  // the step did nothing and the tab became a space; and config/tidy.php, read at the end
  // of the request, overwrote the flags the page had set.

  $myTidyCurl = padCurl ( $padGoExt . 'tags/a_page_may_set_its_own_tidy_settings' );
  $myTidyOut  = padEscape ( trim ( $myTidyCurl ['data'] ) );

?>
