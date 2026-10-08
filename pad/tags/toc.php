<?php

  // {toc} - a table of contents of the page, from its own rendered headings: lib/toc.php.
  //
  //   {toc}                                    the <h2> and <h3>, nested, titled Contents
  //   {toc levels='2-4', title='On this page', list='ul'}
  //
  // levels= names the heading levels, '2,3' when not given; title= the label above the
  // list, '' for none; list= ol (numbered, the default) or ul. The tag prints a marker
  // that is filled in once the whole page has rendered, as {stack} is, so the headings
  // below it are in it - and each of them gets an id to link to when it has none.

  $padTocLevels = padTocLevels ( padTagParm ( 'levels', '2,3' ) );
  $padTocList   = strtolower ( trim ( (string) padTagParm ( 'list', 'ol' ) ) );

  if ( ! $padTocLevels ) {
    if ( $padCheckSyntax )
      padError ( "the toc has no levels '" . padMakeSafe ( padTagParm ( 'levels' ), 20 ) . "' - heading levels 1 to 6, like levels='2,3' or '2-4'" );
    $padTocLevels = [ 2, 3 ];
  }

  if ( ! in_array ( $padTocList, [ 'ol', 'ul' ] ) ) {
    if ( $padCheckSyntax )
      padError ( "the toc has no list '" . padMakeSafe ( $padTocList, 10 ) . "' - ol or ul" );
    $padTocList = 'ol';
  }

  return padTocMarker ( $padTocLevels, (string) padTagParm ( 'title', 'Contents' ), $padTocList );

?>
