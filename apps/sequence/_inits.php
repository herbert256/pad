<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the
  // application's pages - the page it is on marked, its name the title.
  //
  // The regression suite for this subsystem lives with the other framework suites, in the
  // regression application under regression/sequence.

  $sequencePages = [
    'index'     => [ 'Concepts',  'home'      ],
    'sequences' => [ 'Sequences', 'list'      ],
    'actions'   => [ 'Actions',   'zap'       ],
    'examples'  => [ 'Examples',  'code'      ],
    'reference' => [ 'Reference', 'bookmark'  ],
    'gallery'   => [ 'Gallery',   'bar-chart' ],
    'listen'    => [ 'Listen',    'play'      ],
    'guess'     => [ 'Guess',     'help'      ]
  ];

  $frameMenu = [];

  foreach ( $sequencePages as $sequencePage => [ $sequenceLabel, $sequenceIcon ] )
    $frameMenu [] = [ 'link' => "?$sequencePage", 'label' => $sequenceLabel, 'icon' => $sequenceIcon,
                      'current' => $padPage == $sequencePage ];

  $title = $sequencePages [$padPage] [0] ?? $title;

  unset ( $sequencePages, $sequencePage, $sequenceLabel, $sequenceIcon );

?>
