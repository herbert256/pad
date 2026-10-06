<?php

  // JSON writer: the page's data answer (lib/expose.php). Whatever the templates rendered -
  // nothing, when build/expose.php chose this type before they ran - gives way to the
  // variables the page names in $padExpose, as one JSON object, which the web writer then
  // sends with this type's content type, its own ETag and a 304 when the client holds it.

  $padOutput = padExposeJson ();
  $padEtag   = padMD5 ( $padOutput );

  include PAD . 'exits/output/web.php';

?>
