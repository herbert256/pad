<?php

  // Examples live with the subsystem's application. Their own files supply the menu,
  // source panels and playground payload; there is no second copy in the general showcase.
  function showcaseExampleFamilies () {
    $families = [];
    foreach ( glob ( APP . 'examples/*/_about.txt' ) ?: [] as $file ) {
      $family = basename ( dirname ( $file ) );
      $lines = array_map ( 'trim', file ( $file, FILE_IGNORE_NEW_LINES ) );
      $examples = [];
      foreach ( glob ( dirname ( $file ) . '/*.pad' ) ?: [] as $example ) {
        $first = strtok ( (string) file_get_contents ( $example ), "\n" );
        $meta = [];
        foreach ( [ 'title', 'about' ] as $key )
          $meta [$key] = preg_match ( "/$key='((?:[^'\\\\]|\\\\.)*)'/", $first, $match ) ? stripslashes ( $match [1] ) : '';
        $examples [] = [ 'demoPage' => "examples/$family/" . basename ( $example, '.pad' ),
                         'demoTitle' => $meta ['title'], 'demoAbout' => $meta ['about'] ];
      }
      $families [] = [ 'demoFamily' => $lines [0], 'demoDescription' => $lines [1], 'demoExamples' => $examples ];
    }
    return $families;
  }

  function showcaseExampleMenu ( $families, $page ) {
    $menu = [];
    foreach ( $families as $family ) {
      $menu [] = [ 'head' => $family ['demoFamily'] ];
      foreach ( $family ['demoExamples'] as $example )
        $menu [] = [ 'link' => '?' . $example ['demoPage'], 'label' => $example ['demoTitle'],
                    'icon' => '', 'current' => $page === $example ['demoPage'] ];
    }
    return $menu;
  }

  function showcaseExamplePage ( $families, $page ) {
    $flat = [];
    foreach ( $families as $family )
      foreach ( $family ['demoExamples'] as $example )
        $flat [] = $example + [ 'demoFamily' => $family ['demoFamily'] ];
    $index = array_search ( $page, array_column ( $flat, 'demoPage' ), TRUE );
    if ( $index === FALSE ) return [];
    $demo = $flat [$index];
    $source = trim ( preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', file_get_contents ( APP . "$page.pad" ) ) );
    $php = is_file ( APP . "$page.php" ) ? trim ( file_get_contents ( APP . "$page.php" ) ) : '';
    include_once COMMON . '_lib/playground.php';
    return $demo + [ 'title' => $demo ['demoTitle'], 'demoSource' => padHighlightTokens ( $source, 'pad' ),
                    'demoPhp' => padHighlightTokens ( $php, 'php' ), 'demoHasPhp' => $php !== '',
                    'demoPlay' => padPlayLink ( "$source\n", '', $php === '' ? '' : "$php\n", TRUE ),
                    'demoPrev' => $flat [ ( $index + count ( $flat ) - 1 ) % count ( $flat ) ],
                    'demoNext' => $flat [ ( $index + 1 ) % count ( $flat ) ] ];
  }

?>
