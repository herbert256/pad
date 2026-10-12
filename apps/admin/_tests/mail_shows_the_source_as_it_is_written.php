<?php

  // A mail whose text holds an escaped tag - &lt;b&gt; - beside a real one: the source and
  // the text show the bytes of the mail, and the frame the HTML a mail client shows. The
  // escaped tag was decoded once more, to a <b> - in the frame a live element.

  $mailFile = 'adminMailTest' . getmypid () . '.eml';

  @mkdir ( DATA . 'mail/hello', 0777, TRUE );

  file_put_contents ( DATA . "mail/hello/$mailFile",
      "From: a@example.com\r\nTo: b@example.com\r\nSubject: Escaped\r\nMIME-Version: 1.0\r\n"
    . "Content-Type: multipart/alternative; boundary=\"x\"\r\n\r\n"
    . "--x\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\nplain &lt;b&gt; and <i>\r\n"
    . "--x\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n<p>html &lt;b&gt; and <i>it</i></p>\r\n--x--\r\n" );

  register_shutdown_function ( 'unlink', DATA . "mail/hello/$mailFile" );

  $_GET ['app']  = 'hello';
  $_GET ['mail'] = $mailFile;

?>
