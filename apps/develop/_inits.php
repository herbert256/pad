<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the pages
  // that only show something. What builds, cleans, resets or fetches every page - the build,
  // the benchmark, everything with ajax - is started from the index alone, where it says what
  // it does, not one click away in the menu of every page.

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Development', 'icon' => 'home', 'current' => $padPage == 'index' ] ];

  foreach ( [ 'Checks'      => [ 'errors'            => [ 'Errors',            'alert'     ],
                                 'links'             => [ 'Broken links',      'link'      ],
                                 'coverage'          => [ 'Coverage',          'check'     ],
                                 'replay'            => [ 'Replay',            'refresh'   ] ],
              'Performance' => [ 'benchmark/history' => [ 'Benchmark history', 'bar-chart' ] ] ] as $developGroup => $developPages ) {

    $frameMenu [] = [ 'head' => $developGroup ];

    foreach ( $developPages as $developPage => [ $developLabel, $developIcon ] )
      $frameMenu [] = [ 'link' => "?$developPage", 'label' => $developLabel, 'icon' => $developIcon,
                        'current' => $padPage == $developPage ];

  }

  unset ( $developGroup, $developPages, $developPage, $developLabel, $developIcon );

?>
