<?php

  // A numeric character reference in an action is what a browser makes of it: any code
  // point, the semicolon optional - &#13; is a carriage return the URL drops, &#47 a slash,
  // &#1; a control the URL trims off its start - so the first four forms post to
  // elsewhere.example and get no token. PHP's HTML5 decoding leaves &#13; and &#1; as they
  // are and needs the semicolon, and each was taken for a path on this site and handed the
  // session's token.

  $padCsrf = TRUE;

?>
