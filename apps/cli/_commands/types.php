<?php

  // pad types <app> [page ...] [--out=file]: TypeScript declarations of what the pages of an
  // application hold, for the components that read them (pad/lib/typescript.php):
  //
  //   <Page>Vars    the variables of the page's PHP - the sample in _samples/<page>.json, the
  //                 data the props of its islands are made of; a page without one gets it
  //                 captured first, as pad sample captures it
  //   <Page>Answer  the JSON the page answers with padFormat=json - what its $padExpose
  //                 names - when it answers one
  //
  // Without pages, every page that has a sample. The declarations go to standard output, or
  // to --out=file - written whole, its directory made. Run it again when a page's data
  // changes: the types come from the data, so they cannot drift from it for long.

  include_once cliHome () . '/pad/lib/typescript.php';

  $typesApp   = '';
  $typesOut   = '';
  $typesPages = [];

  foreach ( array_slice ( $argv, 2 ) as $typesArg )
    if ( str_starts_with ( $typesArg, '--out=' ) )
      $typesOut = substr ( $typesArg, 6 );
    elseif ( $typesApp === '' )
      $typesApp = $typesArg;
    else
      $typesPages [] = trim ( $typesArg, '/' );

  if ( ! cliApp ( $typesApp ) )
    return cliFail ( "there is no application named '$typesApp' - pad types <app> [page ...] [--out=file]" );

  $typesDir = cliHome () . "/apps/$typesApp/";

  if ( ! $typesPages ) {

    $typesWalk = new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $typesDir, FilesystemIterator::SKIP_DOTS ) );

    foreach ( $typesWalk as $typesFile ) {
      $typesPath = substr ( str_replace ( '\\', '/', $typesFile->getPathname () ), strlen ( $typesDir ) );
      if ( preg_match ( '#^((?:[^_][^/]*/)*)_samples/([^/]+)\.json$#', $typesPath, $typesMatch ) )
        $typesPages [] = $typesMatch [1] . $typesMatch [2];
    }

    sort ( $typesPages );

    if ( ! $typesPages )
      return cliFail ( "no page of '$typesApp' has a sample - name the pages: pad types $typesApp <page> ..." );

  }

  $typesText = "// The types of what the pages of $typesApp hold - written by pad types $typesApp"
             . ( $typesPages ? ' ' . implode ( ' ', $typesPages ) : '' ) . ".\n"
             . "// Made from the data itself: run the command again when a page's data changes.\n";

  foreach ( $typesPages as $typesPage ) {

    $typesAt     = strrpos ( $typesPage, '/' );
    $typesSample = $typesDir . ( $typesAt === FALSE ? '' : substr ( $typesPage, 0, $typesAt + 1 ) )
                 . '_samples/' . ( $typesAt === FALSE ? $typesPage : substr ( $typesPage, $typesAt + 1 ) ) . '.json';

    if ( ! is_file ( $typesSample ) ) {
      [ $typesCode ] = cliRun ( [ 'sample', $typesApp, $typesPage ] );
      if ( $typesCode !== 0 or ! is_file ( $typesSample ) )
        return cliFail ( "the page '$typesPage' of '$typesApp' could not be captured - pad sample $typesApp $typesPage says why" );
      fwrite ( STDERR, "captured " . substr ( $typesSample, strlen ( cliHome () ) + 1 ) . "\n" );
    }

    $typesVars = json_decode ( (string) file_get_contents ( $typesSample ) );

    if ( ! is_object ( $typesVars ) )
      return cliFail ( "the sample of '$typesPage' is no JSON object" );

    $typesText .= "\n// $typesPage - the variables of its PHP\n"
                . padTypeScriptInterface ( padTypeScriptName ( $typesPage, 'Vars' ), $typesVars );

    [ $typesCode, $typesJson ] = cliRun ( [ 'render', $typesApp, $typesPage, 'padFormat=json' ] );

    $typesAnswer = ( $typesCode === 0 ) ? json_decode ( $typesJson ) : NULL;

    if ( is_object ( $typesAnswer ) )
      $typesText .= "\n// $typesPage - its JSON answer, ?$typesPage&padFormat=json\n"
                  . padTypeScriptInterface ( padTypeScriptName ( $typesPage, 'Answer' ), $typesAnswer );

  }

  if ( $typesOut === '' ) {
    cliOut ( rtrim ( $typesText ) );
    return 0;
  }

  if ( ! is_dir ( dirname ( $typesOut ) ) and ! mkdir ( dirname ( $typesOut ), 0755, TRUE ) )
    return cliFail ( "the directory of $typesOut cannot be made" );

  if ( file_put_contents ( $typesOut, $typesText ) === FALSE )
    return cliFail ( "$typesOut cannot be written" );

  fwrite ( STDERR, "wrote $typesOut\n" );

  return 0;

?>
