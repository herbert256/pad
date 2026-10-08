<?php

  // Every page: the menu, a pulldown per chart family. An example page also gets what its
  // three cells show - its data source, its template and, through @page@, the chart - read
  // from the page's own files, so a cell can never drift from what draws the chart.
  //
  // The home page draws every example through {page}, which runs this file again for each:
  // only the first run - the page that was asked for - sets the frame.

  if ( isset ( $chartsFrame ) )
    return;

  $chartsFrame = $padPage;
  $catalog     = chartsCatalog ();
  $families  = [];
  $flat      = [];
  $isExample = FALSE;

  foreach ( $catalog as $family => $entry ) {

    list ( $label, $tagline ) = $entry;

    $examples = end ( $entry );
    $short    = is_string ( $entry [2] ) ? $entry [2] : $label;

    $rows = [];

    foreach ( $examples as $page => list ( $oneTitle, $oneText ) ) {
      $rows [] = [ 'page' => $page, 'title' => $oneTitle, 'text' => $oneText, 'active' => $page == $padPage ];
      $flat [] = [ $page, $oneTitle, $label ];
    }

    $families [] = [ 'family' => $family, 'label' => $label, 'short' => $short, 'tagline' => $tagline,
                     'active' => isset ( $examples [$padPage] ), 'examples' => $rows,
                     'count'  => count ( $rows ) ];

    if ( isset ( $examples [$padPage] ) ) {
      $isExample    = TRUE;
      $familyLabel  = $label;
      $familyLine   = $tagline;
      list ( $exampleTitle, $exampleText ) = $examples [$padPage];
    }

  }

  $exampleCount = count ( $flat );
  $pageTitle    = $isExample ? $exampleTitle : 'Every chart, drawn on the server';

  if ( $isExample ) {

    // Cell 2: the template of the file, its tag's options flowing to the cell's width.

    $tagFile   = "$padPage.pad";
    $tagSource = trim ( file_get_contents ( APP . $tagFile ) );
    $tagHtml   = chartsFlow ( $tagSource );
    $tagPair   = (bool) preg_match ( '#\{/(chart|sparkline)\b#', $tagSource );
    $tagOnly   = $tagPair || ! str_contains ( $tagSource, '<' );

    // Cell 1: the paired PHP when there is one, else the _data file the tag names - and
    // none for a {chart}...{/chart} pair, whose data stands in the tag cell, between the tags.

    $dataFile = $dataLang = $dataSource = '';

    if ( $tagPair )
      ;
    elseif ( file_exists ( APP . "$padPage.php" ) ) {
      $dataFile = "$padPage.php";
      $dataLang = 'php';
    } elseif ( preg_match ( "/data='(\w+)'/", $tagSource, $found ) )
      foreach ( [ 'json', 'yaml' ] as $type )
        if ( file_exists ( APP . "_data/$found[1].$type" ) ) {
          $dataFile = "_data/$found[1].$type";
          $dataLang = $type;
        }

    if ( $dataFile !== '' )
      $dataSource = trim ( file_get_contents ( APP . $dataFile ) );

    // One source cell when there is no data apart from the tag: a pair, or a gauge of one
    // number.

    $oneCell   = $dataFile === '';
    $dataHtml  = padHighlightTokens ( $dataSource, $dataLang );
    $dataLines = substr_count ( $dataSource, "\n" ) + 1;

    // The neighbours, for the pager under the cells.

    $here = array_search ( $padPage, array_column ( $flat, 0 ) );
    list ( $prevPage, $prevTitle ) = $flat [ ( $here + $exampleCount - 1 ) % $exampleCount ];
    list ( $nextPage, $nextTitle ) = $flat [ ( $here + 1 ) % $exampleCount ];

  }

?>
