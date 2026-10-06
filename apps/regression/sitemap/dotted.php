<?php

  // A directory whose name the router cannot take - a dot in it, v1.0/ - holds pages no
  // address reaches: ?v1.0/old answers 404. The sitemap listed them all the same. The
  // directory is made for the moment of one walk and taken away again, so no other page of
  // the tree - the develop crawls of every page - ever meets a page that cannot be asked for.

  $dottedDir = APP . 'v1.0';

  @mkdir ( $dottedDir );
  file_put_contents ( "$dottedDir/old.pad", "an old page\n" );

  $dottedRows = padSitemapPages ();

  unlink ( "$dottedDir/old.pad" );
  rmdir ( $dottedDir );

  $dottedResult = 'v1.0/old ' . ( in_array ( 'v1.0/old', array_column ( $dottedRows, 'page' ), TRUE ) ? 'listed' : 'left out' )
                . ', about ' . ( in_array ( 'about', array_column ( $dottedRows, 'page' ), TRUE ) ? 'listed' : 'left out' );

?>
