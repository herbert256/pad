<?php

  // A {meta sitemap=false} inside a comment is commented out: the page it stands in is
  // listed. The sitemap read the template with a pattern of its own, past comments and
  // {ignore}, and left such a page out. The page is made for the moment of one walk and
  // taken away again, as dotted does with its directory.

  $commentedFile = APP . 'commentedmeta.pad';

  file_put_contents ( $commentedFile, "{# {meta sitemap=false} #}a page\n" );

  $commentedRows = padSitemapPages ();

  unlink ( $commentedFile );

  $commentedResult = 'commentedmeta ' . ( in_array ( 'commentedmeta', array_column ( $commentedRows, 'page' ), TRUE ) ? 'listed' : 'left out' )
                   . ', dotted ' . ( in_array ( 'dotted', array_column ( $commentedRows, 'page' ), TRUE ) ? 'listed' : 'left out' );

?>
