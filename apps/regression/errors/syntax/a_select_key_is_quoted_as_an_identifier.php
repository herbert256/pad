<?php

  // key= names columns, each quoted as an identifier: a backtick in the value is part of
  // the name. It ended the quoted name, and the rest - desc, and a # that comments out what
  // follows - was SQL: the rows came back ordered by salary, descending.

  $padSelect ['staffKeyed'] = [ 'db' => 'staff' ];

  $k = "salary` desc #";

?>
