<?php

  // A page's PHP may ask what {pad:vite} writes, to show it - padViteTags with $once FALSE - and
  // the template's {pad:vite} still writes the same tags: only what a page wrote counts as written.

  $padViteBuild = 'vitefixture';
  $shown        = padViteTags ( [ 'src/admin.js' ], FALSE, FALSE );

?>
