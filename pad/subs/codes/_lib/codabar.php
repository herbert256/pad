<?php

  // Codabar, the barcode of libraries, blood banks and parcel services: digits and - $ : / . +,
  // between a start and a stop letter, A, B, C or D. Each symbol is seven elements - four bars
  // and the three spaces between them - narrow or wide, a wide one three modules to a narrow
  // one's one, and a narrow space stands between symbols. A text that starts and ends with a
  // letter keeps its own; one without letters gets A at both ends.
  //
  //   {codabar '40156'/}               A40156A
  //   {codabar 'B31117013206375B'/}    the letters of its own
  //
  // padCodeCodabar   the bars, or the message why the text is no Codabar

  const PAD_CODABAR_CHARS = '0123456789-$:/.+ABCD';

  // The seven elements of each symbol of PAD_CODABAR_CHARS, a bit per element, the first bar
  // first, 1 a wide one.

  const PAD_CODABAR = [
    0x003, 0x006, 0x009, 0x060, 0x012, 0x042, 0x021, 0x024, 0x030, 0x048,
    0x00C, 0x018, 0x045, 0x051, 0x054, 0x015, 0x01A, 0x029, 0x00B, 0x00E ];

  function padCodeCodabar ( $text ) {

    $text = strtoupper ( $text );
    $ends = 'ABCD';

    if ( $text !== '' and strspn ( $text [0], $ends ) and strlen ( $text ) > 1 and strspn ( substr ( $text, -1 ), $ends ) )
      $data = substr ( $text, 1, -1 );
    elseif ( $text !== '' and ( strspn ( $text [0], $ends ) or strspn ( substr ( $text, -1 ), $ends ) ) )
      return 'a codabar barcode starts and ends with one of the letters A, B, C and D, or with neither - then A is put at both ends';
    else {
      $data = $text;
      $text = "A{$text}A";
    }

    if ( $data === '' or strspn ( $data, '0123456789-$:/.+' ) != strlen ( $data ) )
      return 'a codabar barcode takes digits and - $ : / . + between its start and stop letters';

    $bars = '';

    foreach ( str_split ( $text ) as $k => $char )
      $bars .= ( $k ? '0' : '' ) . padCodeElements ( PAD_CODABAR [ strpos ( PAD_CODABAR_CHARS, $char ) ], 7, 3 );

    return [ $bars, [ [ strlen ( $bars ) / 2, $data ] ], [], 10 ];

  }

?>
