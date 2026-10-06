<?php

  // The {mail} tag, with a transport of the application's own: a function that gets each
  // message - here it keeps them for the template to show. template= looks _mail/ up from
  // this page's directory to the application's, which has welcome and the layout around
  // it; the pair form's content is the HTML part, rendered where it stands - in the loop,
  // with the fields of the row.

  function mailTagsKeep ( $message ) {

    $GLOBALS ['mails'] [] = [ 'to' => $message ['to'], 'subject' => $message ['subject'],
                              'text' => str_replace ( "\n", ' / ', $message ['text'] ), 'html' => $message ['html'] ];

    return TRUE;

  }

  $padMailTransport = 'mailTagsKeep';
  $padMailFrom      = 'shop@example.com';

  $name    = 'Ann';
  $people  = [ [ 'email' => 'bob@example.com', 'first' => 'Bob' ], [ 'email' => 'cy@example.com', 'first' => 'Cy' ] ];
  $mails   = [];

?>
