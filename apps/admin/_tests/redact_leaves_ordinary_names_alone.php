<?php

  // Names that hold key or pass but are no secret - the "key" of an example, passing in a
  // table of players, a template's key: line, bypass: were redacted, and the search no
  // longer found those lines. Names that are secrets still are.

  $plainSamples = [
    '"key": "frameworks"', 'passing: 88', 'key:      {cXs}{key@cXs},{/cXs}', 'bypass: 302', '<monkey>george</monkey>',
    '"apiKey": "s3cr3t"', 'db_pass: s3cr3t', '<password>s3cr3t</password>', '"client_secret": "s3cr3t"', 'private_key: s3cr3t', '"token": "s3cr3t"'
  ];

  $plainSaid = implode ( '; ', array_map ( fn ( $one ) => str_contains ( adminRedact ( $one ), '*REDACTED*' ) ? 'hidden' : 'shown', $plainSamples ) );

?>
