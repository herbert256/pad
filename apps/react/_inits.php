<?php

  // Every page: the title the frame's bar shows unless the page sets its own, the menu of
  // the frame every PAD application shares (_common/_tags/padFrame.pad) - the home page, the
  // patterns and the guide, then the examples under the heading of their group, as
  // _data/examples.json has them - and the version of the files in www/react/.

  $title = 'PAD + React';

  $frameMenu = [ [ 'link' => '?index',    'label' => 'Home',     'icon' => 'home' ],
                 [ 'link' => '?patterns', 'label' => 'Patterns', 'icon' => 'grid' ],
                 [ 'link' => '?guide',    'label' => 'Guide',    'icon' => 'book-open' ] ];

  foreach ( reactGroups () as $reactGroup ) {

    $frameMenu [] = [ 'head' => $reactGroup ['title'] ];

    foreach ( $reactGroup ['examples'] as $reactOne )
      $frameMenu [] = [ 'link' => '?' . $reactOne ['page'], 'label' => $reactOne ['title'], 'icon' => '' ];

  }

  unset ( $reactGroup, $reactOne );

  // The version of www/react/ - when its newest file was saved - for the addresses of the
  // components Babel fetches: a browser keeps such a file a while without asking again, and
  // would run the old copy of a component that changed (pad-react.js). The stylesheet and
  // the runtime carry the version {asset} gives them.
  //
  // The files are the ones the globs *.css, *.js and examples/*.js matched, found in the
  // listings of the two directories PAD keeps between requests (padDirList) - a file added
  // or removed changes its directory and the listing is read again, a file saved changes
  // its own time, which is read every request. The three globs read both directories in
  // every request, some 200 us of each page.

  $reactWww   = dirname ( APPS ) . "/www/$padApp/";
  $reactFiles = [];

  foreach ( [ '' => [ '.css', '.js' ], 'examples/' => [ '.js' ] ] as $reactDir => $reactTypes )
    foreach ( is_dir ( $reactWww . $reactDir ) ? ( padDirList ( $reactWww . $reactDir ) ?: [] ) : [] as $reactFile )
      if ( $reactFile [0] != '.' and in_array ( strrchr ( $reactFile, '.' ), $reactTypes, TRUE ) )
        $reactFiles [] = $reactWww . $reactDir . $reactFile;

  $reactVersion = max ( array_map ( 'filemtime', $reactFiles ) ?: [ 0 ] );

  unset ( $reactDir, $reactTypes, $reactFile );

?>
