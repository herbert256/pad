<?php

  $padCommon = FALSE;

  // Live reload for the sample page only: the index and the verdict are compared by the
  // crawl as they render, and a script carrying a file time would change with every edit.

  $padReload = ( $padPage == 'sample' ) ? 'local' : FALSE;

?>
