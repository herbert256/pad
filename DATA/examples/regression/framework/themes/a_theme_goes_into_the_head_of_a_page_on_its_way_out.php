<?php

  // What exits/output.php does with a theme (padThemeAdd, subs/themes/_lib/theme.php), asked
  // here of a page of its own making - a case is an include request, which never gets a
  // theme, so the request's own state is put aside for each question and put back after:
  // the setting's theme goes in before </head>; on a page without one before its <body>, or
  // after its doctype, or at its start; never into an include request or a page that is no
  // HTML, not at all with $padTheme FALSE, and not into a page that wrote a theme of its own
  // with {widget:theme 'x'/}, whose style stands in its head once tidy moved it there - a box of a
  // {widget:theme}...{/widget:theme} pair is no such theme. The style went in front of the doctype of a page
  // without </head>, which put the browser in quirks mode, and after the page's own theme,
  // which it then outweighed.

  include_once PAD . 'subs/themes/_lib/theme.php';

  $asked = [];
  $kept  = [ $padOutput ?? NULL, $padStop ?? NULL, $padContentType ?? NULL, $padInclude ?? NULL, $padTheme, $padEtag ?? NULL, $padOutputType ];

  foreach ( [ [ 'sepia', FALSE, 'text/html; charset=utf-8', '<html><head><title>t</title></head><body>x</body></html>' ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', '<p>no head</p>' ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', "<!DOCTYPE html>\n<html><body><p>no head</p></body></html>" ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', '<!DOCTYPE html><p>no head, no body</p>' ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', '<html><head><title>t</title>' . padThemeStyle ( 'sepia' ) . '</head><body>x</body></html>' ],
              [ 'ocean', FALSE, 'text/html; charset=utf-8', '<html><head><title>t</title></head><body>' . padThemeStyle ( 'sepia', TRUE ) . '</body></html>' ],
              [ 'sepia', TRUE,  'text/html; charset=utf-8', '<p>an include</p>' ],
              [ 'sepia', FALSE, 'application/json',         '{"a":1}' ],
              [ FALSE,   FALSE, 'text/html; charset=utf-8', '<p>themes off</p>' ],
              [ '',      FALSE, 'text/html; charset=utf-8', '<p>no theme</p>' ] ] as [ $padTheme, $padInclude, $padContentType, $padOutput ] ) {

    $padStop       = 200;
    $padOutputType = 'web';
    $padEtag       = 'before';

    padThemeAdd ();

    foreach ( padThemeTypes () as $one )
      $padOutput = str_replace ( [ padThemeStyle ( $one ), padThemeStyle ( $one, TRUE ) ], [ "[$one]", "[$one box]" ], $padOutput );

    $asked [] = [ 'page' => $padOutput, 'etag' => ( $padEtag == 'before' ) ? 'the same etag' : 'a new etag' ];

  }

  [ $padOutput, $padStop, $padContentType, $padInclude, $padTheme, $padEtag, $padOutputType ] = $kept;

?>
