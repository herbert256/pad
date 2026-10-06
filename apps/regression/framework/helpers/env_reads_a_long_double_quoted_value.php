<?php

  // A double-quoted value of any length is read to its closing quote - a certificate or a
  // JSON key file of a few dozen kilobytes, over many lines, with escaped quotes in it.
  // The closing quote was looked for with one regular expression over the whole value,
  // which ran out of PCRE's JIT stack from about ten thousand characters on: the value
  // came back as never closed, and the warnings of the failed match ended the request.

  $line  = str_repeat ( 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASC', 2 );
  $long  = str_repeat ( "$line\\n", 400 ) . 'end \\"quoted\\"';
  $pairs = padEnvParse ( "BEFORE=1\nKEY=\"$long\"\nMULTI=\"" . str_repeat ( "$line\n", 400 ) . "last\"\nAFTER=2\n" );

  $result = implode ( ' ', [
    strlen ( $pairs ['KEY'] ?? '' ),
    substr_count ( $pairs ['KEY'] ?? '', "\n" ),
    str_ends_with ( $pairs ['KEY'] ?? '', 'end "quoted"' ) ? 'closed' : 'cut',
    substr_count ( $pairs ['MULTI'] ?? '', "\n" ),
    ( $pairs ['BEFORE'] ?? '' ) . ( $pairs ['AFTER'] ?? '' )
  ] );

?>
