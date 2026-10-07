<?php

  // Fixture for the component-injection case: a custom tag that builds its output from a
  // parameter the documented way - into its return value, which is a value and stays text
  // when it lands in the page, braces and all.

  return '[' . padTagParm ( 'title' ) . ']';

?>
