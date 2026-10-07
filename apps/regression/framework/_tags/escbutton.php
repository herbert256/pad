<?php

  // Fixture: CLAUDE.md's _tags/button.php as written - the parameters go into the return
  // value through htmlspecialchars, which must see a quote as the quote it is.

  $label = padTagParm('label', 'Click');
  $href  = padTagParm('href', '#');
  return '<a href="' . htmlspecialchars($href) . '" class="button">' . htmlspecialchars($label) . '</a>';

?>
