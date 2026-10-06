<?php

  // JSON writer: the page's data answer (lib/expose.php) - the variables the page names in
  // $padExpose, as one JSON object, made by exits/exits.php in place of whatever the
  // templates rendered (nothing, when build/expose.php chose this type before they ran) and
  // passed through the output hook - which the web writer sends with this type's content
  // type, its own ETag and a 304 when the client holds it.

  include PAD . 'exits/output/web.php';

?>
