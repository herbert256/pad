<?php

  // A truthy page variable, on purpose: the unset that makes sandbox and clean leave no
  // trace once compared names against snapshot values, loosely, and any TRUE in the page
  // matched every name - so {code sandbox} on a page like this one unset nothing. The
  // sandbox suite could not see it, because a case's snapshot is empty.

  $codePageFlag = TRUE;

?>