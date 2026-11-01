<?php

  // A sheet is read up to 32 MB unpacked: a zip bomb - a sheet of 33 MB of blanks packs into
  // some 33 KB - is refused, where it was read whole into memory (a 99 KB file of 100 MB took
  // 100 MB).

  $xlsxParts = [ 'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . str_repeat ( ' ', 33 * 1048576 ) . '</sheetData></worksheet>' ];

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
