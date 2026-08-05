<?php

  // Loads a local data file into PAD data; the caller sets $padLocalFile to the resolved path.
  // Reached from types/local.php ({local:name}) and from padDataFileData().
  //
  // The name and type options override the file's basename and extension, so a file can be
  // iterated under another name or parsed as another format. A .php file is executed through
  // call/any.php and its return value taken as the data; any other file is read, run as PAD
  // code first - padCode, or padSandbox under the sandbox option - so tags inside it expand,
  // and then handed to padData() for parsing as xml, json, yaml, csv or plain text. The level
  // adopts the data name when it does not have one yet.

  global $pad, $padName;

  $padLocalParts = pathinfo ( $padLocalFile );
  $padLocalName  = padTagParm ( 'name', $padLocalParts ['filename']  ?? '' );
  $padLocalExt   = padTagParm ( 'type', $padLocalParts ['extension'] ?? '' );
  $padLocalBox   = padTagParm ( 'sandbox' );

  if ( $padLocalExt == 'php' ) {

    $padCall      = $padLocalFile;
    $padLocalData = include PAD . 'call/any.php';
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