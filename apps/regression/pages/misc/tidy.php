<?php

  // A whole page goes through Tidy ($padTidy, on by default) - misc/tidied, fetched without
  // padInclude, which skips it. Tidy leaves alone what it has no business rewriting: an
  // element HTML does not define (a Web Component) stays, where it was deleted with only
  // its text left; a <textarea> keeps the leading spaces of its value, where "  two" became
  // "two" and a refilled form posted something else than it showed; a <template> keeps its
  // markup, where a bare <li> got a <ul> round it.

  $tidyPage = padCurl ( $padGoExt . 'misc/tidied' ) ['data'];
  $tidyBody = trim ( preg_replace ( '#^.*<body>|</body>.*$#s', '', $tidyPage ) );

  $tidyResult = implode ( ' | ', [
    'custom element: '  . ( str_contains ( $tidyBody, '<my-app><user-card name="x">hi</user-card></my-app>' ) ? 'kept' : 'LOST' ),
    'textarea: '        . ( str_contains ( $tidyBody, "<textarea name=\"t\">  two\n three</textarea>" ) ? 'kept' : 'CHANGED' ),
    'template: '        . ( str_contains ( $tidyBody, '<template><li>item</li></template>' ) ? 'kept' : 'CHANGED' )
  ] );

?>
