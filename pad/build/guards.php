<?php

  // Runs the _guard.php of every directory in $padBuildDirs, the application root first,
  // before any _inits.php: access control that follows the file tree. One guard in admin/
  // protects every page below it, with no list of routes to keep in step.
  //
  // A guard returns FALSE - or nothing truthy: NULL, 0, '' - to refuse the page; a guard
  // without a return statement, or returning TRUE, lets it through to the next guard down.
  // It can also send the visitor elsewhere itself, padRedirect ( 'login' ). It runs in the
  // request's own scope, after _lib, so it sees the session and request variables and the
  // application's functions; what it echoes is dropped. The first refusal ends the walk
  // and leaves the refusing file in $padBuildRefused.

  $padBuildRefused = '';

  foreach ( $padBuildDirs as $padBuildGuard ) {

    $padCall = "$padBuildGuard/_guard.php";

    if ( ! file_exists ( $padCall ) )
      continue;

    include PAD . 'call/_call.php';

    if ( $padCallPHP !== 1 and ! $padCallPHP ) {
      $padBuildRefused = $padCall;
      break;
    }

  }

?>
