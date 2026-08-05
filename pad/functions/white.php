<?php

  // Pipe function white: collapses every run of whitespace - spaces, tabs, newlines - into a
  // single space. It does not trim the ends; trim does that.

  return preg_replace ('!\s+!', ' ', $value);

?>