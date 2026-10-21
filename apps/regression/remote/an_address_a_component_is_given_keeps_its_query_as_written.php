<?php

  // The value as a visitor sends it, ?q=one&b=evil URL-encoded, which the request made the
  // variable: spliced into the query of an address, it is one parameter's value, not two
  // parameters - restored to its & and =, it sent b=evil to the fetched page.

  $_GET ['q'] = $q = 'one&b=evil';

?>
