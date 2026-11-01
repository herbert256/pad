<?php

  // Format writer: the page's data answer in a format of the formats subsystem
  // (lib/expose.php) - made by exits/exits.php in the format $padOutputFormat names and
  // passed through the output hook - sent by the web writer with the format's content type,
  // its own ETag and a 304 when the client holds it. A format that is a file - a calendar, a
  // spreadsheet - has the name a browser saves it under: the page's as it was asked, with
  // the format's extension, events.ics, orders.xlsx; inline or as an attachment, as the
  // format says. A feed has none.

  $padOutputFormatDef = padFormatDef ( padFormatFind ( (string) $padOutputFormat, 'write' ) );

  if ( ( $padOutputFormatDef ['download'] ?? '' ) !== '' )
    padHeader ( 'Content-Disposition: ' . $padOutputFormatDef ['download'] . '; filename="'
                . str_replace ( '"', '', padMakeSafe ( basename ( $padPageAsked ?? $padPage ), 100 ) )
                . '.' . ( (array) $padOutputFormatDef ['extension'] ) [0] . '"' );

  include PAD . 'exits/output/web.php';

?>
