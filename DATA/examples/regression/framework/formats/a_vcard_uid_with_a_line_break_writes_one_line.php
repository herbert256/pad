<?php

  // A row's uid is written on one UID line with its line breaks taken out, as a URL is: a uid
  // holding CRLF and a property behind it added that property - or a card - to the file. A
  // uid of line breaks alone is no uid, and the card gets one made of the row.

  $vcfUid = padFormatWrite ( 'vcf', [ [ 'name' => 'Ann', 'uid' => "u1\r\nURL:https://evil.example\r\nX-A:b" ],
                                      [ 'name' => 'Bob', 'uid' => "\r\n" ] ] );

  $vcfUid = str_replace ( "\r\n", '~', $vcfUid );

?>
