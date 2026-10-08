<?php

  // One list of rules for both sides. padValidate checks a post against it; for the browser
  // padValidateClient exports the same list, every rule with the message padValidate would
  // give, and {validator} in the template brings the checker that reads it. A good post is
  // kept in the session; the answer is JSON either way - the messages, or the new list.

  $rules = [ 'name'  => 'required|max:40',
             'email' => 'required|email',
             'team'  => 'required|in:web,platform,data',
             'seats' => 'required|integer|min:1|max:8' ];

  $people = padSession ( 'alpinePeople', [] );
  $errors = [];
  $saved  = FALSE;

  if ( padRequestIs ( 'POST' ) ) {

    $errors = padValidate ( $rules );

    if ( ! $errors ) {
      array_unshift ( $people, [ 'name' => trim ( padRequest ( 'name' ) ), 'team' => padRequest ( 'team' ), 'seats' => (int) padRequest ( 'seats' ) ] );
      $saved = padSessionPut ( 'alpinePeople', array_slice ( $people, 0, 8 ) );
      $people = array_slice ( $people, 0, 8 );
    }

  }

  $start     = [ 'rules' => padValidateClient ( $rules ), 'people' => $people ];
  $padExpose = [ 'saved', 'errors', 'people' ];

?>
