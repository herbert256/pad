<?php
  // hostname - Seeded synthetic DNS hostname; suffix defaults to example.test.
  $suffix=padFakeOpt('suffix','example.test');
  if(!is_string($suffix)||strlen($suffix)>200||!preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)*$/D',$suffix)){
    padError('fake:hostname needs an ASCII DNS suffix of at most 200 bytes');return NULL;
  }
  return 'host-'.padFakeNumber(1,99999).'.'.$suffix;
?>
