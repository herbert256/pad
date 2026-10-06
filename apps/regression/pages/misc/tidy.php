<?php

  // A whole page goes through Tidy ($padTidy, on by default) - misc/tidied, fetched without
  // padInclude, which skips it. Tidy leaves alone what it has no business rewriting: an
  // element HTML does not define (a Web Component) stays, where it was deleted with only
  // its text left; a <textarea> keeps the leading spaces of its value, where "  two" became
  // "two" and a refilled form posted something else than it showed; a <template> keeps its
  // markup, where a bare <li> got a <ul> round it - a template inside a template too, which
  // was held up to the inner </template> only, so the outer one lost its close and the rest
  // of the page went into it.

  $tidyPage = padCurl ( $padGoExt . 'misc/tidied' ) ['data'];
  $tidyBody = trim ( preg_replace ( '#^.*<body>|</body>.*$#s', '', $tidyPage ) );

  $tidyResult = implode ( ' | ', [
    'custom element: '  . ( str_contains ( $tidyBody, '<my-app><user-card name="x">hi</user-card></my-app>' ) ? 'kept' : 'LOST' ),
    'textarea: '        . ( str_contains ( $tidyBody, "<textarea name=\"t\">  two\n three</textarea>" ) ? 'kept' : 'CHANGED' ),
    'template: '        . ( str_contains ( $tidyBody, '<template><li>item</li></template>' ) ? 'kept' : 'CHANGED' ),
    'nested template: ' . ( ( str_contains ( $tidyBody, '<template x-if="open"><ul><template x-for="i in items"><li x-text="i"></li></template></ul></template>' )
                              and preg_match ( '#</template>\s*</div>\s*<p>\s*after the component\s*</p>#', $tidyBody ) ) ? 'kept' : 'CHANGED' )
  ] );

?>
