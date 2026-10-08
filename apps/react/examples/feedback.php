<?php

  // The wall is this visitor's own, kept in the session: padSession reads it, padSessionPut
  // writes it. A post comes from the component's fetch - with $padCsrf on in the application's
  // configuration it carries the session's token in an X-CSRF-Token header, or PAD answers 403
  // before this file runs. padValidate answers a message per field that broke its rules;
  // with none the message goes on the wall. The answer to the post is the JSON of $padExpose.

  $wall   = padSession ( 'reactWall', [] );
  $errors = [];
  $saved  = FALSE;

  if ( padRequestIs ( 'POST' ) ) {

    $errors = padValidate ( [ 'name'    => 'required|max:40',
                              'email'   => 'required|email',
                              'mood'    => 'required|in:happy,curious,puzzled',
                              'message' => 'required|min:10|max:280' ] );

    if ( ! $errors ) {

      array_unshift ( $wall, [ 'name'    => trim ( padRequest ( 'name' ) ),
                               'mood'    => padRequest ( 'mood' ),
                               'message' => trim ( padRequest ( 'message' ) ),
                               'at'      => date ( 'H:i' ) ] );

      $wall  = array_slice ( $wall, 0, 12 );
      $saved = padSessionPut ( 'reactWall', $wall );

    }

  }

  $start     = [ 'wall' => $wall ];
  $padExpose = [ 'saved', 'errors', 'wall' ];

?>
