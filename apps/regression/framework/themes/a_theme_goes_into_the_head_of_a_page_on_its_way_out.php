<?php

  // What exits/output.php does with a theme (padThemeAdd, subs/themes/_lib/theme.php), asked
  // here of a page of its own making - a case is an include request, which never gets a
  // theme, so the request's own state is put aside for each question and put back after:
  // the setting's theme goes in before </head>, at the start of a page without one, never
  // into an include request or a page that is no HTML, and not at all with $padTheme FALSE.

  include_once PAD . 'subs/themes/_lib/theme.php';

  $asked = [];
  $kept  = [ $padOutput ?? NULL, $padStop ?? NULL, $padContentType ?? NULL, $padInclude ?? NULL, $padTheme, $padEtag ?? NULL, $padOutputType ];

  foreach ( [ [ 'sepia', FALSE, 'text/html; charset=utf-8', '<html><head><title>t</title></head><body>x</body></html>' ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', '<p>no head</p>' ],
              [ 'sepia', TRUE,  'text/html; charset=utf-8', '<p>an include</p>' ],
              [ 'sepia', FALSE, 'application/json',         '{"a":1}' ],
              [ FALSE,   FALSE, 'text/html; charset=utf-8', '<p>themes off</p>' ],
              [ '',      FALSE, 'text/html; charset=utf-8', '<p>no theme</p>' ] ] as [ $padTheme, $padInclude, $padContentType, $padOutput ] ) {

    $padStop       = 200;
    $padOutputType = 'web';
    $padEtag       = 'before';

    padThemeAdd ();

    foreach ( padThemeTypes () as $one )
      $padOutput = str_replace ( padThemeStyle ( $one ), "[$one]", $padOutput );

    $asked [] = [ 'page' => $padOutput, 'etag' => ( $padEtag == 'before' ) ? 'the same etag' : 'a new etag' ];

  }

  [ $padOutput, $padStop, $padContentType, $padInclude, $padTheme, $padEtag, $padOutputType ] = $kept;

?>
