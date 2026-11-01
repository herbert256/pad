<?php

  // The mail channel hands the @mail@ section to padMail as the HTML part, the text part made
  // from it, the @title@ section as the subject - the file transport keeps it under
  // DATA/mail/, nothing is sent. A recipient without an email gets no mail: FALSE, and the
  // results say why; that is the data's, not the author's, so it is no error.

  padNotify ( [ 'id' => 3, 'email' => 'ann@example.com', 'name' => 'Ann' ], 'shipped',
              [ 'order' => 42, 'items' => 3, 'name' => 'Ann & Bob', 'url' => 'https://shop.example/orders/42' ], [ 'mail' ] );

  $mail = $padMailLast;

  $none   = padNotify ( [ 'id' => 4 ], 'shipped', [ 'order' => 43, 'items' => 1, 'name' => 'Cy', 'url' => '' ], [ 'mail' ] ) ? 'sent' : 'not sent';
  $reason = padNotifyResults () ['mail'] ['error'];

?>
