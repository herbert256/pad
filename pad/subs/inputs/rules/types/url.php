<?php

  // url - a web address: what FILTER_VALIDATE_URL takes, and http or https - a javascript:
  // or file: address is a URL too, and no address a visitor's link should hold.

  return [
    'check'   => fn ( $value ) => filter_var ( $value, FILTER_VALIDATE_URL ) !== FALSE && preg_match ( '#^https?://#i', $value ),
    'message' => ':label must be a valid web address'
  ];

?>
