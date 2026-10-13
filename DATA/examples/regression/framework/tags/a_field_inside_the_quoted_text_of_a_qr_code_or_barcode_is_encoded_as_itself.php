<?php

  // A field inside the quoted text of {qr} or {barcode} - the usual way to make a mailto:
  // or a code from a field - is encoded as the characters it holds, the same symbol as the
  // whole text in a variable gives, where the stand-ins a value travels with went into the
  // code: the @ of the address scanned as U+E040, and a , refused a Code 128.

  $email = 'herbert@example.com';
  $sku   = 'A,B';

?>
