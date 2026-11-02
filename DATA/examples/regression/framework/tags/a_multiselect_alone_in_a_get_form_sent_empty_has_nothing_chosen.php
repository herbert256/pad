<?php

  // A form that asks by GET with its one field a multiselect left empty sends padLists[]
  // alone - a name of the engine's, no question of the form to padFormSource - and the list
  // it names had nothing chosen: value= shown again undid the emptying.

  $_GET ['padLists'] = [ 'tags[]' ];

?>
