<?php

  // The file's own literals, read without building the page - what an access rule or a
  // sitemap generator would ask of a page.

  $metaAccess  = padMeta ( 'access', 'build/padMeta_reads_the_meta_of_a_page_from_its_file' );
  $metaSitemap = var_export ( padMeta ( 'sitemap', 'build/padMeta_reads_the_meta_of_a_page_from_its_file' ), TRUE );

?>
