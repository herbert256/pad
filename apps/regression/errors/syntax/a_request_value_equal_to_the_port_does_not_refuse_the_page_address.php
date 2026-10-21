<?php

  // A request value that merely equals the port - ?amount=9 - chooses nothing: the page's
  // own address on port 9 is fetched (and fails, nothing listens there), where every
  // address on the port of a request's number was refused - ?amount=8000 on pad serve.

  $_GET ['amount'] = '9';

  $u = 'http://127.0.0.1:9/probe';

?>
