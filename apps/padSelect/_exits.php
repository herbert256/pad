<?php

  // After the example's own PHP: the cells read the page's files, so a cell never drifts
  // from what draws the result - the template, the page's PHP when there is one, and the
  // declarations of _lib/select.php its tags reach.

  if ( $isExample ) {

    $tagSource = trim ( file_get_contents ( APP . "$padPage.pad" ) );
    $tagHtml   = padHighlightTokens ( $tagSource, 'pad' );

    $phpSource = file_exists ( APP . "$padPage.php" )
               ? trim ( preg_replace ( '/\A<\?php\R|\?>\s*\z/', '', file_get_contents ( APP . "$padPage.php" ) ), "\r\n" )
               : '';
    $phpSource = $phpSource === '' ? '' : preg_replace ( '/^  /m', '', $phpSource );
    $phpHtml   = $phpSource === '' ? '' : padHighlightTokens ( $phpSource, 'php' );

    // The model's tables and relations this example reaches - less what its own PHP
    // declares, which the cell shows as written.

    $modelTables = array_values ( array_filter (
      psTablesUsed ( $tagSource, $padSelect ),
      fn ( $name ) => isset ( $psSelect [$name] ) and $psSelect [$name] === ( $padSelect [$name] ?? NULL )
    ) );

    $modelSource = $modelTables ? psDeclarations ( $modelTables, $psSelect, $psRelations ) : '';
    $modelHtml   = $modelSource === '' ? '' : padHighlightTokens ( $modelSource, 'php' );

  }

?>
