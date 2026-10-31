<?php

  // ip - An IPv4 address of the three ranges kept for documentation (RFC 5737) -
  // 192.0.2.x, 198.51.100.x and 203.0.113.x - so a log full of them points at no one.

  return padFakePick ( [ '192.0.2', '198.51.100', '203.0.113' ] ) . '.' . padFakeNumber ( 1, 254 );

?>
