<?php

  // Settings for $padOutputType 'web', the normal browser response.
  //
  // Selected by inits/config.php through config/output/$padOutputType.php and consumed by
  // exits/output/web.php and lib/output.php: whether to suppress PAD's own HTTP headers,
  // whether a matching client ETag may be answered with a bare 304, and the Content-Type
  // sent with the body.

  $padWebNoHeaders   = FALSE;
  $padWebEtag304     = TRUE;
  $padContentType    = "text/html; charset=UTF-8";

?>