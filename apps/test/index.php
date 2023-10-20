<?php

  $curl ['url'] = 'http://localhost:8080/?index';

  $curl ['cookies'] ['abc'] = 123;
  $curl ['post'] ['klm'] = 456;
  $curl ['headers'] ['xyz'] = 789;
  $curl ['get'] ['aaa'] = 'bbb';
  $curl ['options'] ['USERAGENT'] = 'My Agent';

  $curl ['cookies'] ['abc2'] = 23;
  $curl ['post'] ['klm2'] = 56;
  $curl ['headers'] ['xyz2'] = 89;
  $curl ['get'] ['aaa2'] = 'ccc';
  $curl ['options'] ['ENCODING'] = 'gzip';

  $abc = padCurl ( $curl );

  dumpp();

?>