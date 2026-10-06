<?php

  $r = json_encode ( [
    padStrSquish ( "  laravel\t\n  framework  " ),
    padStrSquish ( "a\u{00A0}\u{00A0}b\u{3000}c\u{2003}d" ),
    padStrSquish ( "\u{FEFF}\u{200B} trimmed \u{200B}" ),
    padStrSquish ( "keep\u{200B}inside" ) === "keep\u{200B}inside",
    padStrSquish ( '' ),
    padStrSquish ( '0' ),
    padStrSquish ( NULL ),
    padStrSquish ( "   " )
  ], JSON_UNESCAPED_UNICODE );

?>
