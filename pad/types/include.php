<?php

  // Type handler for an application include ({include:name}, or a bare tag matching a file in
  // an _include/ directory): returns the snippet's text through get/include.php, which runs its
  // .php half and appends its .pad half.

  return include PAD . 'get/include.php';

?>