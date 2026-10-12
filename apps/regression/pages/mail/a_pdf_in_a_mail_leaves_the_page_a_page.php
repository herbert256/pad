<?php

  // A mail whose template holds a {pdf}, sent from a page's PHP: the PDF was asked for as
  // the page's own answer, and the visitor got the mail's invoice instead of the page.

  padMail ( 'ann@example.com', 'invoice', 'Invoice' );

  $raw = quoted_printable_decode ( file_get_contents ( $padMailLast ['file'] ) );

  unlink ( $padMailLast ['file'] );

  $mailInvoice = str_contains ( $raw, 'Invoice 42' ) ? 'holds the invoice' : 'without the invoice';

?>
