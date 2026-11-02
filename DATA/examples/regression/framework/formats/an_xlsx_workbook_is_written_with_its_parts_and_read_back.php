<?php

  // A workbook written: unzipped here, its parts in their order, each dated 1980-01-01 00:00,
  // and the sheet's XML - the header row bold (s="1"), an integer, a float and a text that is
  // a number numbers, 007 a text, TRUE a boolean, 2026-11-01 the serial 46327 with the date
  // style and a moment its fraction with the date-time style, a formula's text an inline
  // string and never a <f>, a cell with blanks at its ends xml:space="preserve". Written twice
  // the same bytes; read again the rows, numbers as numbers and dates as dates.

  $xlsxRows = [
    [ 'id' => 1,   'name' => 'Ann',   'amount' => 12.5, 'paid' => TRUE,  'day' => '2026-11-01', 'at' => '2026-11-01T09:30:00', 'code' => '007',  'note' => '=HYPERLINK("http://x")' ],
    [ 'id' => '2', 'name' => ' Bob ', 'amount' => '7',  'paid' => FALSE, 'day' => '1900-01-01', 'at' => '',                    'code' => '1234', 'note' => "two\nlines" ]
  ];

  $xlsxBytes = padFormatWrite ( 'xlsx', $xlsxRows, [ 'sheet' => 'Orders' ] );
  $xlsxSame  = ( $xlsxBytes === padFormatWrite ( 'xlsx', $xlsxRows, [ 'sheet' => 'Orders' ] ) ) ? 'the same bytes' : 'other bytes';

  $xlsxFile = tempnam ( sys_get_temp_dir (), 'padtest' );
  file_put_contents ( $xlsxFile, $xlsxBytes );

  $xlsxZip = new ZipArchive ();
  $xlsxZip->open ( $xlsxFile, ZipArchive::RDONLY );

  // The parts as the zip's local headers have them, their DOS date and time read from the
  // bytes - what libzip makes of them depends on the zone of the machine that reads.

  $xlsxParts = [];

  for ( $xlsxAt = 0; substr ( $xlsxBytes, $xlsxAt, 4 ) == "PK\x03\x04"; ) {
    $xlsxHead     = unpack ( 'vversion/vflags/vmethod/vtime/vdate/Vcrc/Vsize/Vfull/vname/vextra', $xlsxBytes, $xlsxAt + 4 );
    $xlsxParts [] = substr ( $xlsxBytes, $xlsxAt + 30, $xlsxHead ['name'] )
                  . sprintf ( ' %04d-%02d-%02d %02d:%02d', 1980 + ( $xlsxHead ['date'] >> 9 ), ( $xlsxHead ['date'] >> 5 ) & 15, $xlsxHead ['date'] & 31,
                                                            $xlsxHead ['time'] >> 11, ( $xlsxHead ['time'] >> 5 ) & 63 )
                  . ( $xlsxHead ['method'] == 8 ? ' deflated' : ' stored' );
    $xlsxAt      += 30 + $xlsxHead ['name'] + $xlsxHead ['extra'] + $xlsxHead ['size'];
  }

  $xlsxParts = implode ( ', ', $xlsxParts );
  $xlsxBook  = $xlsxZip->getFromName ( 'xl/workbook.xml' );
  $xlsxSheet = $xlsxZip->getFromName ( 'xl/worksheets/sheet1.xml' );

  $xlsxZip->close ();
  unlink ( $xlsxFile );

  preg_match ( '/<sheet [^>]*>/', $xlsxBook, $xlsxName );

  $xlsxName  = $xlsxName [0];
  $xlsxData  = str_replace ( "\n", '~', substr ( $xlsxSheet, strpos ( $xlsxSheet, '<sheetData>' ) ) );
  $xlsxBack  = json_encode ( padFormatRead ( 'xlsx', $xlsxBytes ) );

?>
