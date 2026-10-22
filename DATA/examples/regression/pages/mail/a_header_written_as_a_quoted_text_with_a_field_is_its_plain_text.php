<?php

  // A header of {mail} written as a quoted text with a field in it - subject='Welcome
  // {$name}', to='{$email}' - is the text the field holds: the field's escaping is for the
  // HTML of a page and its stand-ins are for the template, neither for a mail header. The
  // subject went out as "Welcome Zo&euml; O&#039;Neil", and to='{$email}' was refused as
  // 'annexample.com', its @ a stand-in.

  function mailQuotedKeep ( $message ) {

    $GLOBALS ['mails'] [] = json_encode ( [ $message ['to'], $message ['subject'], $message ['replyTo'] ],
                                          JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    return TRUE;

  }

  $padMailTransport = 'mailQuotedKeep';
  $padMailFrom      = 'shop@example.com';

  $name  = "Zoë O'Neil";
  $email = 'ann@example.com';
  $s     = 'Hello, world = "x" | y';
  $mails = [];

?>
