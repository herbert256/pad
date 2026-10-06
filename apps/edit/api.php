<?php

  // The editor's JSON calls: POST ?api&action=<name>&padFormat=json, the arguments as a JSON
  // body (a form for an upload), the session's CSRF token in an X-CSRF-Token header. The
  // call is the file of that name in _api/ (editApi, _lib/api.php); its answer goes out as
  // { "ok": ..., "error": ..., "data": ... } through $padExpose, so no template runs over
  // it - a file's text, braces and all, reaches the browser exactly as it is on disk.
  //
  // A GET - a crawl, pad lint - is answered with the error, not refused: there is nothing
  // to do for it, and nothing wrong with asking.

  $ok    = FALSE;
  $error = '';
  $data  = NULL;

  $padExpose = [ 'ok', 'error', 'data' ];

  $editAction = (string) ( $_GET ['action'] ?? '' );

  if ( ! padRequestIs ( 'POST' ) )
    $error = 'the editor calls are posts';
  elseif ( ! preg_match ( '/^[a-z]+$/', $editAction ) or ! is_file ( APP . "_api/$editAction.php" ) )
    $error = "there is no call named '" . padMakeSafe ( $editAction, 30 ) . "'";
  else
    [ $ok, $error, $data ] = editApi ( $editAction );

?>
