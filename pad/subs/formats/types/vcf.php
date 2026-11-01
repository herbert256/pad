<?php

  // vcf - vCard 4.0 (RFC 6350), read and written - 3.0 cards are read as well: a card a row,
  // name, the parts of the name, org, title, email and phone (the preferred one, and emails
  // and phones all of them), url, the parts of the address, birthday, note and uid; rows as
  // version 4.0 cards, folded at 75 octets with CRLF line ends, a UID made of the row when it
  // has none. Known on sight: a text that opens with BEGIN:VCARD.

  return [
    'name'      => 'vCard',
    'mime'      => 'text/vcard',
    'accept'    => [ 'text/vcard', 'text/x-vcard', 'text/directory' ],
    'extension' => [ 'vcf', 'vcard' ],
    'shape'     => 'rows',
    'lib'       => 'vcf',
    'read'      => fn ( $text, $options ) => padVcfRead ( $text ),
    'write'     => fn ( $data, $options ) => padVcfWrite ( padFormatRows ( $data ) ),
    'sniff'     => [ 'csv', fn ( $text ) => (bool) preg_match ( '/\ABEGIN:VCARD[ \t]*\r?\n/i', $text ) ],
    'download'  => 'inline'
  ];

?>
