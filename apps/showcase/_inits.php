<?php

  // Every page: the menu, a pulldown per group of tags. An example page also gets what its
  // cells show - its data source, its template and, through @page@, the result - read from
  // the page's own files, so a cell can never drift from what draws it. The {meta} line
  // that names the example is left out of the template cell: it belongs to the showcase.
  //
  // The home page draws every example through {page}, which runs this file again for each:
  // only the first run - the page that was asked for - sets the frame.

  if ( isset ( $showcaseFrame ) )
    return;

  $showcaseFrame = $padPage;
  $catalog       = showcaseCatalog ();
  $families      = [];
  $sections      = [];
  $flat          = [];
  $isExample     = FALSE;
  $tagCount      = 0;

  foreach ( showcaseGroups () as $group => $tagline ) {

    $rows   = [];
    $active = FALSE;

    foreach ( $catalog [$group] as $tag => list ( $label, $about, $examples ) ) {

      $tagCount++;
      $cards = [];

      foreach ( $examples as $page => list ( $oneTitle, $oneText ) ) {

        $rows  [] = [ 'page' => $page, 'title' => $oneTitle, 'text' => $oneText, 'active' => $page == $padPage ];
        $cards [] = [ 'page' => $page, 'title' => $oneTitle, 'text' => $oneText ];
        $flat  [] = [ $page, $oneTitle, $label ];

        if ( $page == $padPage ) {
          $active       = TRUE;
          $isExample    = TRUE;
          $familyLabel  = $label;
          $exampleTitle = $oneTitle;
          $exampleText  = $oneText;
        }

      }

      $sections [] = [ 'family' => $tag, 'label' => $label, 'tagline' => $about, 'examples' => $cards ];

    }

    if ( $rows )
      $families [] = [ 'family' => $group, 'label' => $group, 'short' => $group, 'tagline' => $tagline,
                       'active' => $active, 'examples' => $rows, 'count' => count ( $rows ) ];

  }

  $exampleCount = count ( $flat );
  $pageTitle    = $isExample ? $exampleTitle : 'Tags that draw, format and interact';

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

    $here = array_search ( $padPage, array_column ( $flat, 0 ) );
    list ( $prevPage, $prevTitle ) = $flat [ ( $here + $exampleCount - 1 ) % $exampleCount ];
    list ( $nextPage, $nextTitle ) = $flat [ ( $here + 1 ) % $exampleCount ];

  }

?>
