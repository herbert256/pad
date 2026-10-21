<?php

  // Every name of the fixture's template sent as a request value as well - a list for prime.
  // Each tag stays what the template wrote; the page's own $ucwords stays its field, and
  // ?who=ann, a name the template means nothing else by, is the field it always was.

  $curl = padCurl ( $padGoExt . 'request/tagnames&padInclude&PHP_INT_SIZE=EVIL&upper=EVIL&fibonacci=EVIL'
                  . '&prime%5B%5D=EVIL&nums=EVIL&exclaim=EVIL&strrev=EVIL&strtoupper=EVIL&ucwords=EVIL&who=ann' );

  $tagNamesResult = $curl ['result'] . ' ' . padEscape ( trim ( $curl ['data'] ) );

?>
