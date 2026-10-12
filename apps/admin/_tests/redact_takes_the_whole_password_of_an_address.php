<?php

  // Passwords in addresses with an @ or a / in them, unencoded: the files page showed "ss" of
  // p@ss and all of se/cr/et. A port and an @ in a path are no password.

  $addressSamples = [ "'https://ann:p@ss@example.com/r.git'", "'https://ann:se/cr/et@example.com/r.git'", "'mysql://root:hunter2@localhost/db'",
                      "'https://example.com:8080/@scope/x'", "'https://example.com/team/@scope/r.git'" ];

  $addressSaid = implode ( ' ', array_map ( 'adminRedact', $addressSamples ) );

?>
