<?php

  // A cell's column is at most XFD, 16384, the last a sheet has: ZZZZ1 is refused, where it
  // made every row 475254 fields wide.

  $xlsxParts = [ 'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . '<row r="1"><c r="A1" t="inlineStr"><is><t>name</t></is></c><c r="ZZZZ1"><v>1</v></c></row>' . '</sheetData></worksheet>' ];

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
