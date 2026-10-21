<?php

  // The provider the misc/react_keeps_its_data_with_a_byte_that_is_no_utf8 test runs: a
  // Latin-1 text, as a latin1 database or file hands it, beside a plain value.

  return [ 'name' => "Caf\xE9", 'n' => 1 ];

?>
