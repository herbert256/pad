<?php

  // The file transport, the default: the message is written as an .eml under
  // DATA/mail/<app>/ instead of being sent. padMail renders _mail/order - found beside this
  // page, before the application's own _mail/ - with its PHP first, the text part from
  // order.txt inside _inits.txt; a mail with only an HTML body gets a text part made from
  // it. Read back from the file, headers and parts decoded; the files are removed again.

  function mailFileShow ( $file ) {

    $raw = file_get_contents ( $file );

    unlink ( $file );

    list ( $head, $body ) = explode ( "\r\n\r\n", $raw, 2 );

    $show = [];

    foreach ( explode ( "\r\n", $head ) as $line )
      if ( preg_match ( '/^(To|Subject|From|Cc|Bcc|Reply-To|MIME-Version):/', $line ) )
        $show [] = $line;

    preg_match_all ( '/Content-Type: (text\/\w+); charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n(.*?)\r\n--pad-/s', $body, $parts, PREG_SET_ORDER );

    foreach ( $parts as $part )
      $show [] = $part [1] . ': ' . str_replace ( "\r\n", ' / ', quoted_printable_decode ( $part [2] ) );

    return implode ( "\n", $show );

  }

  $padMailFrom = 'The shop <shop@example.com>';

  padMail ( 'ann@example.com', 'order', 'Your order 42 - Zoë', [ 'number' => 42, 'customer' => 'Zoë', 'amount' => 12.5 ] );

  echo mailFileShow ( $padMailLast ['file'] ), "\n", 'variables left: ', isset ( $number ) ? 'yes' : 'no', "\n\n";

  padMail ( 'Bob Smith <bob@example.com>, carol@example.com', '', 'Hello', [],
            [ 'html' => '<p>Hello <b>Bob</b> &amp; Carol</p><ul><li>one</li><li>two</li></ul>',
              'from' => 'Shop <shop@example.com>', 'cc' => 'dave@example.com', 'bcc' => 'eve@example.com', 'replyTo' => 'help@example.com' ] );

  echo mailFileShow ( $padMailLast ['file'] );

?>
