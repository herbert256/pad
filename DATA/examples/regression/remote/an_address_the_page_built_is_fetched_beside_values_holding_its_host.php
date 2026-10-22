<?php

  // An address the page's PHP built is fetched though request values merely hold its host
  // as text - a search for "regression-dark", a cookie "my regression": only a value that
  // is the host, its port or the start of the address chose it. Before, any value holding
  // the host refused the page, a short host even by the random padReqID cookie.

  $_GET ['q']         = 'regression-dark';
  $_COOKIE ['theme'] = 'my regression';

  $u = 'SELF://regression/remote/?stamp';

?>
