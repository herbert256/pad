<?php

  // A secret of twelve thousand characters - a key in a .json, a certificate in a .php: the
  // pattern repeated a group per character and ran out of PCRE's JIT stack, the redaction
  // answered nothing and the search of every application and the files page failed.

  $longValue = str_repeat ( 'A', 12000 );
  $longJson  = adminRedact ( "{ \"name\": \"shop\", \"privateKey\": \"$longValue\" }" );
  $longPhp   = adminRedact ( "<?php \$keyData = '$longValue'; \$name = 'shop'; ?>" );

  $longSaid  = ( str_contains ( $longJson, '"privateKey": "*REDACTED*"' ) && str_contains ( $longJson, '"name": "shop"' ) ? 'json redacted' : 'json wrong' )
             . ', ' . ( str_contains ( $longPhp, "\$keyData = '*REDACTED*'" ) && str_contains ( $longPhp, "'shop'" ) ? 'php redacted' : 'php wrong' )
             . ', ' . ( str_contains ( $longJson . $longPhp, 'AAAA' ) ? 'the value shown' : 'the value hidden' );

?>
