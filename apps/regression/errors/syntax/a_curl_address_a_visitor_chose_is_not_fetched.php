<?php

  // {curl} fetches an address written in the template, not one a visitor chose: {curl $v}
  // with a request value http://127.0.0.1:9/probe had the server connect to any host and
  // port it can reach - its own pages behind an address guard, cloud metadata - and write
  // the answer into the page, where data=$v refused the same value.

  $_GET ['v'] = $v = 'http://127.0.0.1:9/probe';

?>
