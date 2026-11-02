<?php

  // Every page: the menu of the frame every PAD application shares
  // (_common/_tags/padFrame.pad) - the home page, then a heading per chart family with a link
  // per example under it - and the title in the frame's bar. An example page also gets what
  // its three cells show - its data source, its template and, through @page@, the chart -
  // read from the page's own files, so a cell can never drift from what draws the chart.
  //
  // The home page draws every example through {pad:page}, which runs this file again for each:
  // only the first run - the page that was asked for - sets the frame.

  if ( isset ( $chartsFrame ) )
    return;

  $chartsFrame = $padPage;
  $catalog     = chartsCatalog ();
  $families    = [];
  $flat        = [];
  $isExample   = FALSE;

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All charts', 'icon' => 'bar-chart', 'current' => $padPage == 'index' ] ];

  foreach ( $catalog as $family => $entry ) {

    list ( $label, $tagline, $examples ) = $entry;

    $rows         = [];
    $frameMenu [] = [ 'head' => $label ];

    foreach ( $examples as $page => list ( $oneTitle, $oneText ) ) {
      $rows      [] = [ 'page' => $page, 'title' => $oneTitle, 'text' => $oneText ];
      $flat      [] = [ $page, $oneTitle, $label ];
      $frameMenu [] = [ 'link' => "?$page", 'label' => $oneTitle, 'icon' => '', 'current' => $page == $padPage ];
    }

    $families [] = [ 'family' => $family, 'label' => $label, 'tagline' => $tagline, 'examples' => $rows ];

    if ( isset ( $examples [$padPage] ) ) {
      $isExample    = TRUE;
      $familyLabel  = $label;
      list ( $exampleTitle, $exampleText ) = $examples [$padPage];
    }

  }

  $exampleCount = count ( $flat );
  $kindCount    = count ( padChartTypes () );
  $title        = $isExample ? $exampleTitle : 'Every chart, drawn on the server';

  if ( $isExample ) {

    // Cell 2: the template of the file, its tag's options flowing to the cell's width.

    $tagFile   = "$padPage.pad";
    $tagSource = trim ( file_get_contents ( APP . $tagFile ) );
    $tagHtml   = chartsFlow ( $tagSource );
    $tagPair   = (bool) preg_match ( '#\{/(chart|sparkline)\b#', $tagSource );
    $tagOnly   = $tagPair || ! str_contains ( $tagSource, '<' );

    // Cell 1: the paired PHP when there is one, else the _data file the tag names - and
    // none for a {chart:bar}...{/chart:bar} pair, whose data stands in the tag cell, between the tags.

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

    // The icon at the top right of the example that takes it to the WASM editor
    // (_common/_lib/playground.php): the template, with the paired PHP in the PHP box or the
    // _data file it names in the data box, under that name.

    include_once COMMON . '_lib/playground.php';

    $playLink = padPlayLink ( "$tagSource\n",
                              ( $dataFile !== '' and $dataLang != 'php' ) ? padPlayData ( $found [1], APP . $dataFile ) : '',
                              ( $dataLang == 'php' ) ? "$dataSource\n" : '', TRUE );

    // The neighbours, for the pager under the cells.

    $here = array_search ( $padPage, array_column ( $flat, 0 ) );
    list ( $prevPage, $prevTitle ) = $flat [ ( $here + $exampleCount - 1 ) % $exampleCount ];
    list ( $nextPage, $nextTitle ) = $flat [ ( $here + 1 ) % $exampleCount ];

  }

?>
