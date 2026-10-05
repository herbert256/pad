<?php

  // Pipe function trans(count): the value as a key of the _lang/ catalogs, translated into
  // the request's locale - {echo 'cart.title' | trans}, {echo 'cart.items' | trans($n)},
  // where the count picks the plural form and replaces %d. padTrans in lib/locale.php; the
  // {trans} tag takes named substitutions as well.

  $padTransVars = [];

  if ( $count > 0 )
    $padTransVars ['count'] = $parm [0];

  return padTrans ( (string) $value, $padTransVars );

?>
