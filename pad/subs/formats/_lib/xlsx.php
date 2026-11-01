<?php

  // XLSX - Office Open XML spreadsheets (ECMA-376, SpreadsheetML) - the writer and the reader
  // of types/xlsx.php. Both need PHP's zip extension (ZipArchive).
  //
  // The writer makes a workbook of one sheet: a header row of the column names in bold,
  // frozen at the top, then a row per row. A cell is what its value is: an integer or a float
  // a number - and so is a text that is one, written without a leading zero or a +, as a
  // database answers numbers (007 and +31 stay text); TRUE and FALSE booleans; a date,
  // 2026-11-01, or a moment, 2026-11-01T09:30 - written as its clock shows it, a zone left
  // off, Excel having none - a date with a date format; any other text a text, an inline
  // string, never a formula: =HYPERLINK(...) in a cell is the text it is. The columns are as
  // wide as their longest text. The zip is the same bytes for the same rows: its parts in a
  // fixed order, each dated 1980-01-01, the first time a zip can hold.
  //
  // The reader takes the first sheet - or sheet=, by name or number - its first row the
  // names, a row per row below it: shared strings, inline strings, numbers (an integer where
  // it is whole), booleans, the text of a formula's result, and a number in a date format as
  // the date it is, 2026-11-01 or 2026-11-01T09:30:00. A cell under no name is named after its
  // column, B; an empty row is no row.

  const padXlsxMain = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
  const padXlsxRel  = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
  const padXlsxPkg  = 'http://schemas.openxmlformats.org/package/2006/relationships';

  function padXlsxWrite ( $rows, $options ) {

    if ( ! class_exists ( 'ZipArchive' ) )
      padFormatFail ( 0, "PHP's zip extension is not loaded - an xlsx is a zip" );

    $columns = padFormatColumns ( $rows );
    $widths  = [];
    $lines   = [];

    $lines [] = padXlsxRow ( 1, array_map ( fn ( $name ) => [ 'text', $name ], $columns ), 1, $widths );

    foreach ( $rows as $index => $row )
      $lines [] = padXlsxRow ( $index + 2, array_map ( fn ( $column ) => padXlsxCell ( $row [$column] ?? NULL ), $columns ), 0, $widths );

    $last  = padXlsxColumn ( max ( 1, count ( $columns ) ) ) . ( count ( $rows ) + 1 );
    $cols  = '';

    foreach ( $widths as $index => $width )
      $cols .= '<col min="' . ( $index + 1 ) . '" max="' . ( $index + 1 ) . '" width="' . min ( 60, max ( 8, $width + 2 ) ) . '" customWidth="1"/>';

    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
           . '<worksheet xmlns="' . padXlsxMain . '" xmlns:r="' . padXlsxRel . '">'
           . '<dimension ref="A1:' . $last . '"/>'
           . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
           . '<sheetFormatPr defaultRowHeight="15"/>'
           . ( $cols !== '' ? "<cols>$cols</cols>" : '' )
           . '<sheetData>' . implode ( '', $lines ) . '</sheetData>'
           . '</worksheet>';

    $name = padXlsxSheetName ( (string) ( $options ['sheet'] ?? 'Sheet1' ) );

    $parts = [
      '[Content_Types].xml'        => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
                                    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                                    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                                    . '<Default Extension="xml" ContentType="application/xml"/>'
                                    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                                    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                                    . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                                    . '</Types>',
      '_rels/.rels'                => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
                                    . '<Relationships xmlns="' . padXlsxPkg . '">'
                                    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                                    . '</Relationships>',
      'xl/workbook.xml'            => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
                                    . '<workbook xmlns="' . padXlsxMain . '" xmlns:r="' . padXlsxRel . '">'
                                    . '<bookViews><workbookView/></bookViews>'
                                    . '<sheets><sheet name="' . padFormatXml ( $name ) . '" sheetId="1" r:id="rId1"/></sheets>'
                                    . '</workbook>',
      'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
                                    . '<Relationships xmlns="' . padXlsxPkg . '">'
                                    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                                    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                                    . '</Relationships>',
      'xl/styles.xml'              => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
                                    . '<styleSheet xmlns="' . padXlsxMain . '">'
                                    . '<numFmts count="2"><numFmt numFmtId="164" formatCode="yyyy\-mm\-dd"/><numFmt numFmtId="165" formatCode="yyyy\-mm\-dd\ hh:mm:ss"/></numFmts>'
                                    . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
                                    . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
                                    . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
                                    . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                                    . '<cellXfs count="4">'
                                    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                                    . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
                                    . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                                    . '<xf numFmtId="165" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                                    . '</cellXfs>'
                                    . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
                                    . '</styleSheet>',
      'xl/worksheets/sheet1.xml'   => $sheet
    ];

    return padXlsxZip ( $parts );

  }

  // The zip of the parts: written by ZipArchive to a file of its own, every part deflated and
  // dated 1980-01-01 00:00 - 315532800, the first second a zip's DOS date holds - and the
  // bytes read back. libzip writes that date as the C library's local time, which the TZ of
  // the environment decides, so TZ is UTC while it writes: on a server an hour east of UTC
  // the parts were dated 01:00, and the same rows were other bytes on another machine.

  function padXlsxZip ( $parts ) {

    $file = tempnam ( sys_get_temp_dir (), 'padxlsx' );
    $zip  = new ZipArchive ();
    $tz   = getenv ( 'TZ' );

    putenv ( 'TZ=UTC' );

    try {

      if ( $zip->open ( $file, ZipArchive::OVERWRITE ) !== TRUE )
        padFormatFail ( 0, 'the zip cannot be made in ' . sys_get_temp_dir () );

      foreach ( $parts as $name => $xml ) {
        $zip->addFromString ( $name, $xml );
        $zip->setCompressionName ( $name, ZipArchive::CM_DEFLATE );
        $zip->setMtimeName ( $name, 315532800 );
      }

      $zip->close ();

      return file_get_contents ( $file );

    } finally {
      putenv ( $tz === FALSE ? 'TZ' : "TZ=$tz" );
      @unlink ( $file );
    }

  }

  function padXlsxSheetName ( $name ) {

    $name = trim ( preg_replace ( '/[\[\]:*?\/\\\\]/', ' ', $name ), " '" );

    return mb_substr ( $name === '' ? 'Sheet1' : $name, 0, 31 );

  }

  // A value as a cell: [ kind, value ] - number, bool, date (a serial with its style), text -
  // or NULL for an empty one.

  function padXlsxCell ( $value ) {

    if ( $value === NULL or $value === '' )
      return NULL;

    if ( is_bool ( $value ) )
      return [ 'bool', $value ? '1' : '0' ];

    if ( is_int ( $value ) )
      return [ 'number', (string) $value ];

    if ( is_float ( $value ) )
      return is_finite ( $value ) ? [ 'number', padFormatCell ( $value ) ] : [ 'text', is_nan ( $value ) ? 'NAN' : ( $value > 0 ? 'INF' : '-INF' ) ];

    if ( $value instanceof DateTimeInterface )
      return padXlsxSerial ( $value->format ( 'Y-m-d H:i:s' ), TRUE );

    if ( is_array ( $value ) or is_object ( $value ) )
      return [ 'text', padFormatCell ( $value ) ];

    $text = (string) $value;

    if ( preg_match ( '/^-?(0|[1-9]\d{0,14})(\.\d+)?$/D', $text ) )
      return [ 'number', $text ];

    if ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)$/D', $text, $m ) and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) and $m [1] >= 1900 )
      return padXlsxSerial ( "$text 00:00:00", FALSE );

    if ( preg_match ( '/^(\d{4})-(\d\d)-(\d\d)[T ](\d\d):(\d\d)(?::(\d\d)(?:\.\d+)?)?(?:Z|[+-]\d\d:?\d\d)?$/D', $text, $m )
         and checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] ) and $m [1] >= 1900 and $m [4] < 24 and $m [5] < 60 and ( $m [6] ?? 0 ) < 60 )
      return padXlsxSerial ( "{$m[1]}-{$m[2]}-{$m[3]} {$m[4]}:{$m[5]}:" . ( ( $m [6] ?? '' ) !== '' ? $m [6] : '00' ), TRUE );

    return [ 'text', $text ];

  }

  // A moment as Excel counts it: days since 1899-12-30 - the 1900 system, whose 29 February
  // 1900 that never was is past for every date from March 1900 - the time a part of the day.

  function padXlsxSerial ( $wall, $time ) {

    $moment  = new DateTimeImmutable ( $wall, new DateTimeZone ( 'UTC' ) );
    $seconds = (int) $moment->format ( 'U' );
    $days    = intdiv ( $seconds - ( $seconds % 86400 + 86400 ) % 86400, 86400 ) + 25569;
    $rest    = ( $seconds % 86400 + 86400 ) % 86400;

    if ( $days < 61 )
      $days--;

    if ( ! $time or $rest == 0 )
      return [ 'date', (string) $days, $time ? 3 : 2 ];

    return [ 'date', padFormatCell ( $days + $rest / 86400 ), 3 ];

  }

  function padXlsxRow ( $number, $cells, $style, &$widths ) {

    $out = '';

    foreach ( $cells as $index => $cell ) {

      if ( $cell === NULL )
        continue;

      $ref   = padXlsxColumn ( $index + 1 ) . $number;
      $shown = ( $cell [0] == 'date' ) ? 19 : max ( array_map ( 'mb_strlen', explode ( "\n", $cell [1] ) ) );

      $widths [$index] = max ( $widths [$index] ?? 0, $shown );

      if ( $cell [0] == 'number' )
        $out .= "<c r=\"$ref\"" . ( $style ? " s=\"$style\"" : '' ) . "><v>{$cell[1]}</v></c>";
      elseif ( $cell [0] == 'bool' )
        $out .= "<c r=\"$ref\" t=\"b\"" . ( $style ? " s=\"$style\"" : '' ) . "><v>{$cell[1]}</v></c>";
      elseif ( $cell [0] == 'date' )
        $out .= "<c r=\"$ref\" s=\"{$cell[2]}\"><v>{$cell[1]}</v></c>";
      else {
        $space = ( $cell [1] !== trim ( $cell [1] ) or str_contains ( $cell [1], "\n" ) ) ? ' xml:space="preserve"' : '';
        $out  .= "<c r=\"$ref\" t=\"inlineStr\"" . ( $style ? " s=\"$style\"" : '' ) . "><is><t$space>" . padFormatXml ( $cell [1] ) . '</t></is></c>';
      }

    }

    return "<row r=\"$number\">$out</row>";

  }

  // A column's letters: 1 is A, 27 AA.

  function padXlsxColumn ( $number ) {

    $letters = '';

    for ( ; $number > 0; $number = intdiv ( $number - 1, 26 ) )
      $letters = chr ( 65 + ( $number - 1 ) % 26 ) . $letters;

    return $letters;

  }

  function padXlsxColumnNumber ( $letters ) {

    $number = 0;

    foreach ( str_split ( strtoupper ( $letters ) ) as $letter )
      $number = $number * 26 + ord ( $letter ) - 64;

    return $number;

  }

  // The reader.

  function padXlsxRead ( $bytes, $options ) {

    if ( ! class_exists ( 'ZipArchive' ) )
      padFormatFail ( 0, "PHP's zip extension is not loaded - an xlsx is a zip" );

    if ( ! str_starts_with ( $bytes, "PK\x03\x04" ) )
      padFormatFail ( 0, 'the data is no zip - an xlsx is one' );

    $file = tempnam ( sys_get_temp_dir (), 'padxlsx' );
    $zip  = new ZipArchive ();

    try {

      file_put_contents ( $file, $bytes );

      if ( $zip->open ( $file, ZipArchive::RDONLY ) !== TRUE )
        padFormatFail ( 0, 'the zip cannot be opened' );

      $part = fn ( $name ) => $zip->getFromName ( $name );

      $workbook = padXlsxXml ( $part ( 'xl/workbook.xml' ), 'xl/workbook.xml' );
      $rels     = padXlsxXml ( $part ( 'xl/_rels/workbook.xml.rels' ), 'xl/_rels/workbook.xml.rels' );
      $target   = padXlsxSheetPart ( $workbook, $rels, $options ['sheet'] ?? '' );
      $sheet    = padXlsxXml ( $part ( $target ), $target );
      $strings  = ( $zip->locateName ( 'xl/sharedStrings.xml' ) !== FALSE ) ? padXlsxStrings ( padXlsxXml ( $part ( 'xl/sharedStrings.xml' ), 'xl/sharedStrings.xml' ) ) : [];
      $dates    = ( $zip->locateName ( 'xl/styles.xml' ) !== FALSE ) ? padXlsxDateStyles ( padXlsxXml ( $part ( 'xl/styles.xml' ), 'xl/styles.xml' ) ) : [];
      $base1904 = in_array ( (string) ( $workbook->workbookPr ['date1904'] ?? '' ), [ '1', 'true' ], TRUE );

      $zip->close ();

    } finally {
      @unlink ( $file );
    }

    $grid = [];

    foreach ( $sheet->sheetData->row ?? [] as $row ) {

      $number = (int) $row ['r'];
      $column = 0;

      foreach ( $row->c as $c ) {

        $column = isset ( $c ['r'] ) ? padXlsxColumnNumber ( preg_replace ( '/\d+$/', '', (string) $c ['r'] ) ) : $column + 1;
        $value  = padXlsxValue ( $c, $strings, $dates, $base1904 );

        if ( $value !== NULL )
          $grid [ $number ?: count ( $grid ) + 1 ] [$column] = $value;

      }

    }

    if ( ! $grid )
      return [];

    ksort ( $grid );

    $header = array_shift ( $grid );
    $width  = max ( array_keys ( $header ) );

    foreach ( $grid as $cells )
      $width = max ( $width, max ( array_keys ( $cells ) ) );

    $names = [];

    for ( $column = 1; $column <= $width; $column++ )
      $names [$column] = ( isset ( $header [$column] ) and (string) $header [$column] !== '' ) ? (string) $header [$column] : padXlsxColumn ( $column );

    $rows = [];

    foreach ( $grid as $cells ) {
      $row = [];
      foreach ( $names as $column => $name )
        $row [$name] = $cells [$column] ?? '';
      $rows [] = $row;
    }

    return $rows;

  }

  function padXlsxXml ( $xml, $name ) {

    if ( $xml === FALSE )
      padFormatFail ( 0, "the spreadsheet has no $name" );

    $errors = libxml_use_internal_errors ( TRUE );

    try {
      $doc   = simplexml_load_string ( $xml, 'SimpleXMLElement', LIBXML_NONET );
      $error = libxml_get_errors () [0] ?? NULL;
      libxml_clear_errors ();
    } finally {
      libxml_use_internal_errors ( $errors );
    }

    if ( $doc === FALSE )
      padFormatFail ( 0, "$name cannot be read" . ( $error ? ' - line ' . $error->line . ': ' . trim ( $error->message ) : '' ) );

    return $doc;

  }

  // The part of the sheet asked for - by its name, by its number from 1, the first when none.

  function padXlsxSheetPart ( $workbook, $rels, $want ) {

    $sheets = [];

    foreach ( $workbook->sheets->sheet ?? [] as $sheet )
      $sheets [] = [ (string) $sheet ['name'], (string) $sheet->attributes ( padXlsxRel ) ['id'] ];

    if ( ! $sheets )
      padFormatFail ( 0, 'the workbook has no sheet' );

    $chosen = $sheets [0];

    if ( (string) $want !== '' ) {
      $chosen = NULL;
      foreach ( $sheets as $index => $sheet )
        if ( $sheet [0] === (string) $want or ( ctype_digit ( (string) $want ) and $index + 1 == (int) $want ) )
          $chosen = $sheet;
      if ( $chosen === NULL )
        padFormatFail ( 0, "the workbook has no sheet '" . padMakeSafe ( $want, 31 ) . "' - " . implode ( ', ', array_column ( $sheets, 0 ) ) );
    }

    foreach ( $rels->Relationship ?? [] as $rel )
      if ( (string) $rel ['Id'] === $chosen [1] ) {
        $target = (string) $rel ['Target'];
        return str_starts_with ( $target, '/' ) ? ltrim ( $target, '/' ) : 'xl/' . $target;
      }

    padFormatFail ( 0, "the sheet '{$chosen[0]}' has no part" );

  }

  // The shared strings: a <si> each, its text or the text of its runs - the phonetic ones
  // left out.

  function padXlsxStrings ( $doc ) {

    $strings = [];

    foreach ( $doc->si as $si )
      $strings [] = padXlsxRich ( $si );

    return $strings;

  }

  function padXlsxRich ( $node ) {

    if ( isset ( $node->t ) )
      return (string) $node->t;

    $text = '';

    foreach ( $node->r as $run )
      $text .= (string) $run->t;

    return $text;

  }

  // The cell formats that show a date: [ index => 'date' | 'time' | 'moment' ] - the built-in
  // date formats and every format of the workbook whose code has days, months, years, hours
  // or seconds outside its quoted texts and [ ] parts.

  function padXlsxDateStyles ( $doc ) {

    $codes = [];

    foreach ( $doc->numFmts->numFmt ?? [] as $format )
      $codes [ (int) $format ['numFmtId'] ] = (string) $format ['formatCode'];

    $kinds = [];

    foreach ( $doc->cellXfs->xf ?? [] as $index => $xf ) {

      $id = (int) $xf ['numFmtId'];

      if ( in_array ( $id, [ 14, 15, 16, 17 ], TRUE ) )
        $kinds [] = 'date';
      elseif ( in_array ( $id, [ 18, 19, 20, 21, 45, 46, 47 ], TRUE ) )
        $kinds [] = 'time';
      elseif ( $id == 22 )
        $kinds [] = 'moment';
      elseif ( isset ( $codes [$id] ) ) {
        $code = preg_replace ( '/"[^"]*"|\[[^\]]*\]|\\\\.|_.|\*./', '', $codes [$id] );
        $day  = (bool) preg_match ( '/[dy]/i', $code ) || preg_match ( '/m/i', $code ) && ! preg_match ( '/[hs]/i', $code );
        $time = (bool) preg_match ( '/[hs]/i', $code );
        $kinds [] = $day ? ( $time ? 'moment' : 'date' ) : ( $time ? 'time' : '' );
      } else
        $kinds [] = '';

    }

    return $kinds;

  }

  function padXlsxValue ( $c, $strings, $dates, $base1904 ) {

    $type = (string) ( $c ['t'] ?? 'n' );
    $v    = isset ( $c->v ) ? (string) $c->v : NULL;

    if ( $type == 'inlineStr' )
      return isset ( $c->is ) ? padXlsxRich ( $c->is ) : NULL;

    if ( $v === NULL )
      return NULL;

    if ( $type == 's' )
      return $strings [ (int) $v ] ?? '';

    if ( $type == 'b' )
      return ( $v === '1' || strtolower ( $v ) === 'true' );

    if ( $type == 'str' or $type == 'e' or $type == 'd' )
      return $v;

    if ( ! is_numeric ( $v ) )
      return $v;

    $kind = $dates [ (int) ( $c ['s'] ?? 0 ) ] ?? '';

    if ( $kind !== '' )
      return padXlsxDate ( (float) $v, $kind, $base1904 );

    if ( preg_match ( '/^-?\d+$/D', $v ) and ( $int = filter_var ( $v, FILTER_VALIDATE_INT ) ) !== FALSE )
      return $int;

    return (float) $v;

  }

  // A serial as the date it is: a whole one a day, one under 1 a time, any other a moment.

  function padXlsxDate ( $serial, $kind, $base1904 ) {

    $timeOnly = ( $kind == 'time' or $serial < 1 );

    if ( $base1904 )
      $serial += 1462;
    elseif ( $serial < 61 )
      $serial += 1;

    $seconds = (int) round ( ( $serial - 25569 ) * 86400 );
    $moment  = new DateTimeImmutable ( '@' . $seconds );

    if ( $timeOnly )
      return $moment->format ( 'H:i:s' );

    if ( $kind == 'date' or $seconds % 86400 == 0 )
      return $moment->format ( 'Y-m-d' );

    return $moment->format ( 'Y-m-d\TH:i:s' );

  }

?>
