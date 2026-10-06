<?php

  // The session's own secrets travel under names no pattern knows: the CSRF token is the
  // value of a hidden field in every level of a rendered form, of $padCsrfIssued and of
  // whatever variable a page keeps it in, and PAD's session id rides the PAD header line.
  // An error report of a remote visitor's request carried that visitor's token in five files.

  // A session id PAD minted for this request - one the browser brought is the visitor's own
  // text, which is not redacted by its value (redaction_hides_no_text_a_visitor_chose).

  $padSesID = padRandomString ();
  $token    = padCsrfToken ();
  $redacted = padRedact ( [
    'form' => '<input type="hidden" name="padCsrfToken" value="' . $token . '">',
    'kept' => $token,
    'pad'  => [ "PAD: $padSesID-$padReqID" ]
  ] );

  $redacted = str_replace ( $padReqID, 'REQ', json_encode ( $redacted, JSON_UNESCAPED_SLASHES ) );

?>
