<?php

  // The contacts are the session's copy of _data/contacts.json. htmx asks for one row at a
  // time - &id= with &padFragment=row - and this file narrows the loop to that row, so the
  // first {fragment 'row'} to render is the answer: the row to read, the row as a form
  // (mode=edit), or after a post the row as stored - or the form again with padValidate's
  // messages in it. The token of the post comes in the X-CSRF-Token header (_inits.pad).

  $contacts = padSession ( 'htmxContacts', htmxData ( 'contacts' ) );
  $id       = (int) padRequest ( 'id', 0 );
  $mode     = padRequest ( 'mode', '' ) === 'edit' ? 'edit' : 'view';
  $errors   = [];

  if ( padRequestIs ( 'POST' ) and padRequestHas ( 'reset' ) ) {
    $contacts = htmxData ( 'contacts' );
    padSessionPut ( 'htmxContacts', $contacts );
    $id = 0;
  }

  elseif ( padRequestIs ( 'POST' ) and $id ) {

    $errors = padValidate ( [ 'name' => 'required|max:40', 'email' => 'required|email', 'city' => 'required|max:30' ] );

    foreach ( $contacts as $at => $contact )
      if ( $contact ['id'] == $id and ! $errors ) {
        $contacts [$at] = [ 'id' => $id, 'name' => trim ( padRequest ( 'name' ) ), 'email' => trim ( padRequest ( 'email' ) ), 'city' => trim ( padRequest ( 'city' ) ) ];
        padSessionPut ( 'htmxContacts', $contacts );
      }

    $mode = $errors ? 'edit' : 'view';

  }

  // The rows the template walks: all of them, or the one asked for - a form of it showing
  // the posted values and the messages when the post had errors.

  $rows = [];

  foreach ( $contacts as $contact ) {

    if ( $id and $contact ['id'] != $id )
      continue;

    if ( $errors )
      $contact = array_replace ( $contact, [ 'name'  => (string) padRequest ( 'name' ),
                                             'email' => (string) padRequest ( 'email' ),
                                             'city'  => (string) padRequest ( 'city' ) ] );

    $rows [] = $contact + [ 'editing'  => ( $id and $mode == 'edit' ),
                            'errName'  => $errors ['name']  ?? '',
                            'errEmail' => $errors ['email'] ?? '',
                            'errCity'  => $errors ['city']  ?? '' ];

  }

?>
