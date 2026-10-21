<?php

  // Fixture for the custom group: an application function named like a built-in pipe that
  // answers HTML of its own - a search-term highlighter. It comes before the built-in, and
  // what it returns is the application's text, which the field's sanitize chain escapes.

  return '<mark>' . $value . '</mark>';

?>
