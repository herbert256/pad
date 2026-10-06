<?php

  // Fixture for the component-injection case: a custom tag whose PHP builds its content from
  // a parameter value, the documented _tags pattern. The value must stay text when it lands
  // in that content, which the engine re-scans as template.

  $padContent = '[' . padTagParm ( 'title' ) . ']';

  return TRUE;

?>
