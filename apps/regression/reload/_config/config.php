<?php

  $padCommon = FALSE;

  // Live reload for the sample page and the routed item/[id] only: the index and the
  // verdict are compared by the crawl as they render, and a script carrying a file time
  // would change with every edit.

  $padReload = in_array ( $padPage, [ 'sample', 'item/[id]' ] ) ? 'local' : FALSE;

?>
