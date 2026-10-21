<?php

  // The second parameter of {data} is a source as data= is: a value naming an address,
  // here this server's own, was fetched and stored.

  // The value as a visitor sends it, ?v=..., which the request made the variable: the
  // page's own values name the author's address or range.

  $_GET ['v'] = $v = 'SELF://regression/errors/?syntax/a_data_type_must_exist';

?>
