<?php

  // A post of form f: its city shows the item posted in place of value=, the city of form g,
  // which did not come back, shows value=; town came back without a choice - a radio group
  // with none is left out of the post, the padLists[] beside it says it was there - so none
  // is chosen, not value= again; and place was
  // posted a value that is no item of it, which is not shown. The page's padValidate left a
  // message for the town, tied to its radios.

  $_SERVER ['REQUEST_METHOD'] = 'POST';
  $_POST = [ 'padForm' => 'f', 'city' => 'Paris', 'place' => 'Tokyo', 'padLists' => [ 'city', 'town', 'place' ] ];

  padValidate ( [ 'town' => 'required' ] );

?>
