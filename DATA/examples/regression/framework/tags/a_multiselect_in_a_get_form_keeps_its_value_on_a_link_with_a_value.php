<?php

  // The page of a form that asks by GET, asked with a value of its own - a sort link,
  // ?page&sort=name - and padLists[] naming two of its lists, as the form sends them: tags,
  // not named, shows value=, where the value in the query emptied it; emptied, named and
  // not in the query, has nothing chosen; chosen shows what the query holds.

  $_GET ['sort']     = 'name';
  $_GET ['padLists'] = [ 'emptied[]', 'chosen[]' ];
  $_GET ['chosen']   = [ 'c' ];

?>
