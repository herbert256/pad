<?php

  // Fixture: a custom tag that echoes its parameter rather than returning it. What a tag
  // echoes is template source, so a value in it must not open a tag.

  echo '<b>' . htmlspecialchars ( padTagParm ( 'label' ) ) . '</b>';

  return TRUE;

?>
