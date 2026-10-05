<?php

  // The pages in this directory open only to a request that says ?pass=yes.

  return ( $_GET ['pass'] ?? '' ) === 'yes';

?>
