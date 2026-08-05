<?php

  // Expands a 1..10 (or a..z) range into a PAD data array of its members. Included by
  // padData() as data/<type>.php; padContentType picks 'range' for two alphanumeric parts
  // separated by '..'. All the work is padGetRange in lib/template.php.

  return padGetRange ( $data );

?>