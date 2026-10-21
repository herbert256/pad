<?php

  // Fixture: an application tag that reads its parameters directly, as APP.md shows - the
  // first in $padParm, the second in $padOpt, a named one in $padPrm - and echoes them. What
  // a tag echoes is template source, so a value in them must not open a tag.

  echo '<b>' . htmlspecialchars ( $padParm ) . '|' . htmlspecialchars ( $padOpt [$pad] [2] ?? '' )
     . '|' . htmlspecialchars ( $padPrm [$pad] ['label'] ?? '' ) . '</b>';

  return TRUE;

?>
