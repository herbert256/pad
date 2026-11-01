<?php

  // The shared strings of a workbook are at most 500000: 500001 empty ones - 6.5 MB, packed
  // into some 7 KB - are refused, where each was kept.

  $xlsxParts = [ 'xl/sharedStrings.xml' => '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . str_repeat ( '<si><t/></si>', 500001 ) . '</sst>' ];

  $xlsxFile = tempnam ( sys_get_temp_dir (), 'padcase' );
  file_put_contents ( $xlsxFile, padFormatWrite ( 'xlsx', [ [ 'a' => 1 ] ] ) );
  $xlsxZip = new ZipArchive ();
  $xlsxZip->open ( $xlsxFile );
  foreach ( $xlsxParts as $xlsxName => $xlsxPart )
    $xlsxZip->addFromString ( $xlsxName, $xlsxPart );
  $xlsxZip->close ();
  $xlsxBytes = file_get_contents ( $xlsxFile );
  unlink ( $xlsxFile );

  $sheet = count ( padFormatRead ( 'xlsx', $xlsxBytes ) );

?>
