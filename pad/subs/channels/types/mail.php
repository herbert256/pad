<?php

  // mail - the notification as an e-mail, through padMail and the transport it is set up with
  // ($padMailTransport - a file under DATA/mail/ by default): the @mail@ section, or the plain
  // text, is the HTML part, the text part is made from it, the title is the subject. It goes
  // to the recipient's email.

  return [
    'settings' => [ 'email' => [ 'to' => 'email' ] ],
    'send'     => function ( $message, $settings ) {
      $sent = padMail ( $settings ['email'], '', $message ['title'] !== '' ? $message ['title'] : $message ['name'], [],
                        [ 'html' => $message ['html'], 'text' => $message ['text'] ] );
      return $sent ? TRUE : 'padMail did not send it';
    },
  ];

?>
