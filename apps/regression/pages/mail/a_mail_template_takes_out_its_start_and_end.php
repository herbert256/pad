<?php

  // A mail template renders once, as a page does, so its own @start@ and @end@ come out.
  // Shown as a value, so the page's own marker handling cannot be what takes them out.

  padMail ( 'ann@example.com', 'sections', 'Sections' );

  $raw = file_get_contents ( $padMailLast ['file'] );

  unlink ( $padMailLast ['file'] );

  preg_match ( '/Content-Type: text\/html; charset=UTF-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n(.*?)\r\n--pad-/s', $raw, $part );

  $mailHtml = quoted_printable_decode ( $part [1] ?? '' );

?>
