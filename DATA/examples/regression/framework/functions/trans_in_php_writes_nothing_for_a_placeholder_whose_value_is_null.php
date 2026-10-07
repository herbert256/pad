<?php

  // A NULL value - a database column without one - fills its placeholder with nothing, as
  // the {trans} tag fills it; padTrans called from PHP left the ':name' in the text.

  $padLocale = 'nl';

  $r = json_encode ( [
    padTrans ( 'greeting', [ 'name' => NULL, 'place' => 'Leiden' ] ),
    padTrans ( 'cart.items', [ 'count' => NULL ] )
  ] );

?>
