<?php

  // The rows read are at most 500000 cells: a name in the first column and one in the last,
  // XFD, over 31 rows is 507904 cells, refused, where they were made - 16384 fields a row.

  $xlsxParts = [ 'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . '<row r="1"><c r="A1" t="inlineStr"><is><t>first</t></is></c><c r="XFD1" t="inlineStr"><is><t>last</t></is></c></row>' . implode ( '', array_map ( fn ( $n ) => "<row r=\"$n\"><c r=\"A$n\"><v>$n</v></c></row>", range ( 2, 32 ) ) ) . '</sheetData></worksheet>' ];

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
