<?php

  // A mail rendered in the page's PHP is an output of its own: a {progress} in it writes
  // the style into the mail, and the page's {progress} still writes it into the page -
  // where the mail took the one style of the request, and the page's bar lost its colours.

  padMail ( 'ann@example.com', 'progress', 'Progress' );

  $raw = file_get_contents ( $padMailLast ['file'] );

  unlink ( $padMailLast ['file'] );

  $mailStyles = substr_count ( quoted_printable_decode ( $raw ), '--pad-progress-track:' ) ? 'with its style' : 'without a style';

?>
