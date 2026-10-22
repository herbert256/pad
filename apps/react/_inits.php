<?php

  // Every page: the title the wrapper writes unless the page sets its own, the section of
  // the menu that is lit - the first part of the page's name, the examples belonging to the
  // patterns - and the version of the files in www/react/.

  $title      = 'PAD + React';
  $navSection = explode ( '/', $padPage ) [0];

  if ( $navSection == 'examples' )
    $navSection = 'patterns';

  // The version of www/react/ - when its newest file was saved - for the addresses of the
  // stylesheet and the scripts: a browser keeps such a file a while without asking again,
  // and would run the old copy of a component that changed (pad-react.js).
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
