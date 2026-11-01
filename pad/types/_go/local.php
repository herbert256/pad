<?php

  // Loads a local data file into PAD data; the caller sets $padLocalFile to the resolved path.
  // Reached from types/local.php ({local:name}) and from padDataFileData().
  //
  // The name and type options override the file's basename and extension, so a file can be
  // iterated under another name or parsed as another format. A .php file is executed through
  // call/any.php and its return value taken as the data; any other file is read, run as PAD
  // code first - padCode, or padSandbox under the sandbox option - so tags inside it expand,
  // and then handed to padData() for parsing as xml, json, yaml, csv, a format of the
  // formats subsystem by its extension (toml, ics, md ... - lib/format.php) or plain text;
  // a format of bytes, an xlsx, is read as the file holds it. The level
  // adopts the data name when it does not have one yet. A .sql file is a named query, run on
  // the application database by padDbNamed with its {$field} placeholders bound.

  global $pad, $padName;

  $padLocalParts = pathinfo ( $padLocalFile );
  $padLocalName  = padTagParm ( 'name', $padLocalParts ['filename']  ?? '' );
  $padLocalExt   = padTagParm ( 'type', $padLocalParts ['extension'] ?? '' );
  $padLocalBox   = padTagParm ( 'sandbox' );

  if ( $padLocalExt == 'php' ) {

    $padCall      = $padLocalFile;
    $padLocalData = include PAD . 'call/any.php';
    $padLocalExt  = '';

  } elseif ( $padLocalExt == 'sql' ) {

    // A named query is never run as PAD: a field spliced into its text would be SQL.
    // padDbNamed binds each {$field} through db()'s placeholders instead.

    $padLocalData = padDbNamed ( padFileGet ( $padLocalFile ), $padLocalFile );
    $padLocalExt  = '';

  } elseif ( padFormatDef ( padFormatFind ( $padLocalExt, 'read' ) ) ['binary'] ?? FALSE ) {

    // A format of bytes - a spreadsheet, sales.xlsx (lib/format.php) - is read as the file
    // holds it: run as PAD, or trimmed and taken for text by padData, the zip was no zip.
    // The options its reader takes come from the tag: {local:sales.xlsx sheet='Notes'}.

    $padLocalOptions = [];

    foreach ( padFormatDef ( padFormatFind ( $padLocalExt, 'read' ) ) ['options'] ?? [] as $padLocalOption )
      if ( ( $padLocalValue = padTagParm ( $padLocalOption ) ) !== '' )
        $padLocalOptions [$padLocalOption] = $padLocalValue;

    $padLocalData = padFormatRead ( $padLocalExt, padFileGet ( $padLocalFile ), $padLocalOptions );
    $padLocalExt  = '';

  } else

    if ( $padLocalBox )
      $padLocalData = padSandbox ( padFileGet ($padLocalFile) );
    else
      $padLocalData = padCode ( padFileGet ($padLocalFile) );

  if ( $padLocalName and ! $padName [$pad] )
    $padName [$pad] = $padLocalName;

  return padData ( $padLocalData, $padLocalExt, $padLocalName );

?>
