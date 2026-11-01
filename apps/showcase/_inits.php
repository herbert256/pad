<?php

  // Every page: the menu of the frame every PAD application shares
  // (_common/_tags/padFrame.pad) - the home page, then a heading per group of tags with a link
  // per example under it - and the title in the frame's bar. An example page also gets what
  // its cells show - its data source, its template and, through @page@, the result - read
  // from the page's own files, so a cell can never drift from what draws it. The {meta} line
  // that names the example is left out of the template cell: it belongs to the showcase.
  //
  // The home page draws every example through {page}, which runs this file again for each:
  // only the first run - the page that was asked for - sets the frame.

  if ( isset ( $showcaseFrame ) )
    return;

  $showcaseFrame = $padPage;
  $catalog       = showcaseCatalog ();
  $sections      = [];
  $flat          = [];
  $isExample     = FALSE;
  $tagCount      = 0;

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All tags', 'icon' => 'grid', 'current' => $padPage == 'index' ] ];

  foreach ( showcaseGroups () as $group ) {

    if ( $catalog [$group] )
      $frameMenu [] = [ 'head' => $group ];

    foreach ( $catalog [$group] as $tag => list ( $label, $about, $examples ) ) {

      $tagCount++;
      $cards = [];

      foreach ( $examples as $page => list ( $oneTitle, $oneText ) ) {

        $cards     [] = [ 'page' => $page, 'title' => $oneTitle, 'text' => $oneText ];
        $flat      [] = [ $page, $oneTitle, $label ];
        $frameMenu [] = [ 'link' => "?$page", 'label' => $oneTitle, 'icon' => '', 'current' => $page == $padPage ];

        if ( $page == $padPage ) {
          $isExample    = TRUE;
          $familyLabel  = $label;
          $exampleTitle = $oneTitle;
          $exampleText  = $oneText;
        }

      }

      $sections [] = [ 'family' => $tag, 'label' => $label, 'tagline' => $about, 'examples' => $cards ];

    }

  }

  $exampleCount = count ( $flat );
  $title        = $isExample ? $exampleTitle : 'Tags that draw, format and interact';

  if ( $isExample ) {

    // The template, as it stands in the file, less its {meta} line.

    $tagFile   = "$padPage.pad";
    $tagSource = trim ( preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', file_get_contents ( APP . $tagFile ) ) );
    $tagHtml   = padHighlightTokens ( $tagSource, 'pad' );
    $tagPair   = FALSE;
    $tagOnly   = ! str_contains ( $tagSource, '<' );

    // The paired PHP when there is one, else the _data file the tag names.

    $dataFile = $dataLang = $dataSource = '';

    if ( file_exists ( APP . "$padPage.php" ) ) {
      $dataFile = "$padPage.php";
      $dataLang = 'php';
    } elseif ( preg_match ( "/data='(\w+)'/", $tagSource, $found ) )
      foreach ( [ 'json', 'yaml', 'csv' ] as $type )
        if ( file_exists ( APP . "_data/$found[1].$type" ) ) {
          $dataFile = "_data/$found[1].$type";
          $dataLang = $type;
        }

    if ( $dataFile !== '' )
      $dataSource = trim ( file_get_contents ( APP . $dataFile ) );

    $oneCell   = $dataFile === '';
    $dataHtml  = padHighlightTokens ( $dataSource, $dataLang );

    // The icon at the top right of the example that takes it to the playground
    // (_common/_lib/playground.php): the template, with the paired PHP in the PHP box or the
    // _data file it names in the data box, under that name.

    include_once COMMON . '_lib/playground.php';

    $playLink = padPlayLink ( "$tagSource\n",
                              ( $dataFile !== '' and $dataLang != 'php' ) ? padPlayData ( $found [1], APP . $dataFile ) : '',
                              ( $dataLang == 'php' ) ? "$dataSource\n" : '', TRUE );

    $here = array_search ( $padPage, array_column ( $flat, 0 ) );
    list ( $prevPage, $prevTitle ) = $flat [ ( $here + $exampleCount - 1 ) % $exampleCount ];
    list ( $nextPage, $nextTitle ) = $flat [ ( $here + 1 ) % $exampleCount ];

  }

?>
