<?php

  // Windows line ends and a byte order mark, a value holding =, an empty quoted value, a
  // comment after the closing quote, an escape that is none of the six, a single-quoted
  // value over two lines, dotted keys, and ${...} from the real environment.

  $text = "\xEF\xBB\xBF# comment\r\n"
        . "URL=https://example.com/?a=1&b=2\r\n"
        . "EMPTY_QUOTED=\"\"   # nothing\r\n"
        . "UNKNOWN=\"a\\qb\"\r\n"
        . "SINGLE='one\r\ntwo'\r\n"
        . "app.name=Dotted\r\n"
        . "\texport\tTABBED=yes\r\n"
        . "METHOD=\"\${REQUEST_METHOD}-\${app.name}\"\r\n"
        . "ESCAPED=\"\\\${REQUEST_METHOD}\"\r\n"
        . "WORDS=TRUE\r\n"
        . "QUOTED_WORD='null'\r\n";

  $pairs = json_encode ( padEnvParse ( $text ), JSON_UNESCAPED_SLASHES );
  $none  = json_encode ( padEnvParse ( "# only a comment\n\n" ) );

?>
