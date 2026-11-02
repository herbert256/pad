<?php

  // A workbook as Excel writes one, read as its sheet streams by: shared strings of runs, a
  // phonetic run left out, an inline string of runs, a number, a date, a moment and a time by
  // their styles, booleans, a formula's text and an error value - and a row without its r
  // the row after the one before, its cells without their r one after the other, where each
  // cell of such a row became a row of its own; an element named row in an extension of the
  // sheet is no row.

  $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

  $xlsxParts = [
    'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="' . $main . '"><sheetData>'
      . '<row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c><c r="C1" t="inlineStr"><is><t>when</t></is></c>'
      . '<c r="D1" t="inlineStr"><is><r><t>ok</t></r><r><rPr><b/></rPr><t>?</t></r></is></c><c r="E1" t="s"><v>2</v></c></row>'
      . '<row r="2"><c r="A2" t="s"><v>3</v></c><c r="B2"><v>12.5</v></c><c r="C2" s="1"><v>46327</v></c><c r="D2" t="b"><v>1</v></c>'
      . '<c r="E2" t="str"><f>A2&amp;"x"</f><v>Annx</v></c></row>'
      . '<row><c t="s"><v>4</v></c><c><v>7</v></c><c s="2"><v>46327.5</v></c><c t="b"><v>0</v></c><c t="e"><v>#DIV/0!</v></c></row>'
      . '<row r="5"><c r="B5" s="3"><v>0.75</v></c><c r="E5"/></row>'
      . '</sheetData><extLst><ext uri="x"><row><c r="A9"><v>99</v></c></row></ext></extLst></worksheet>',
    'xl/sharedStrings.xml' => '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="' . $main . '" count="5" uniqueCount="5">'
      . '<si><t>name</t></si><si><r><t>am</t></r><r><rPr><i/></rPr><t>ount</t></r></si><si><t xml:space="preserve"> sp </t></si>'
      . '<si><t>Ann</t><rPh sb="0" eb="1"><t>an</t></rPh></si><si><r><t>Bo</t></r><r><t>b</t></r><rPh sb="0" eb="1"><t>bob</t></rPh></si></sst>',
    'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8"?><styleSheet xmlns="' . $main . '"><numFmts count="1"><numFmt numFmtId="164" formatCode="yyyy-mm-dd hh:mm"/></numFmts>'
      . '<cellXfs count="4"><xf numFmtId="0"/><xf numFmtId="14"/><xf numFmtId="164"/><xf numFmtId="20"/></cellXfs></styleSheet>'
  ];

  $xlsxFile = tempnam ( sys_get_temp_dir (), 'padcase' );
  file_put_contents ( $xlsxFile, padFormatWrite ( 'xlsx', [ [ 'a' => 1 ] ] ) );
  $xlsxZip = new ZipArchive ();
  $xlsxZip->open ( $xlsxFile );
  foreach ( $xlsxParts as $xlsxName => $xlsxPart )
    $xlsxZip->addFromString ( $xlsxName, $xlsxPart );
  $xlsxZip->close ();
  $xlsxBytes = file_get_contents ( $xlsxFile );
  unlink ( $xlsxFile );

  $xlsxRows = json_encode ( padFormatRead ( 'xlsx', $xlsxBytes ), JSON_UNESCAPED_SLASHES );

?>
