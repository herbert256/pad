<?php

  // pad lint <app> [dir]: every page of the application - or of one directory of it -
  // rendered under the strict syntax check, whatever the application chose for itself, and
  // every error listed with its place in the template. No server: each page runs as pad
  // render in a child process of its own, four at a time, since an error ends the process
  // it happens in. Exit status 1 when any page failed.
  //
  // The pages are the ones the suites walk: .pad, .html and .php files outside the _
  // directories, less the action-only fixtures. A page is run with its PHP, as a request
  // would run it - one that needs a form posted, a login or a database it cannot reach
  // shows up here as the error it would give.

  $lintApp = $argv [2] ?? '';
  $lintSub = trim ( $argv [3] ?? '', '/' );

  if ( ! cliApp ( $lintApp ) )
    return cliFail ( "there is no application named '$lintApp' - pad lint <app> [dir]" );

  if ( $lintSub !== '' and ( ! cliName ( $lintSub ) or ! is_dir ( cliHome () . "/apps/$lintApp/$lintSub" ) ) )
    return cliFail ( "there is no directory '$lintSub' in $lintApp" );

  $lintPages   = cliPages ( $lintApp, $lintSub );
  $lintResult  = cliRunPages ( $lintApp, $lintPages, [ 'PAD_LINT' => '1' ] );

  $lintFailed = 0;

  foreach ( $lintResult as $lintPage => list ( $lintStatus, $lintOut ) ) {

    if ( $lintStatus === 0 ) {
      cliOut ( "ok    $lintPage" );
      continue;
    }

    $lintFailed++;

    $lintJson = json_decode ( trim ( $lintOut ), TRUE );
    $lintText = is_array ( $lintJson ) ? (string) ( $lintJson ['error'] ?? '' ) : trim ( strtok ( trim ( $lintOut ), "\n" ) );

    cliOut ( "FAIL  $lintPage  " . rtrim ( preg_replace ( '/^PAD: /', '', $lintText ) ) );

    $lintWhere = is_array ( $lintJson ) ? ( $lintJson ['template'] ?? [] ) : [];

    if ( isset ( $lintWhere ['file'] ) )
      cliOut ( "      " . $lintWhere ['file'] . ':' . $lintWhere ['line'] . ':' . $lintWhere ['column']
             . '  ' . $lintWhere ['tag']
             . ( ( $lintWhere ['suggest'] ?? '' ) ? '  - did you mean ' . $lintWhere ['suggest'] . '?' : '' ) );

  }

  cliOut ( "\n" . count ( $lintResult ) . " pages, $lintFailed failed" );

  return $lintFailed ? 1 : 0;

?>
