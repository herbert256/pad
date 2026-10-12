<?php

  // A field written inside the quoted text of {avatar}, {gravatar}, {identicon} or {qr}
  // gives what the field alone gives: the characters it holds - not the stand-ins it
  // travels with, nor the HTML its sanitize step made of it, which went into the initials,
  // the hash and the code, and into a label escaped once more.

  $name  = 'Åsa Ørsted & Co';
  $email = "o'brien@example.com";
  $query = 'https://example.com/?a=1&b=2';

?>
