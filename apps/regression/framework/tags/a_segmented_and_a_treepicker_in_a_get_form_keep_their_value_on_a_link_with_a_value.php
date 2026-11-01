<?php

  // The page of a form that asks by GET, asked with a value of its own - a pager link,
  // ?page&page=2 - and padLists[] naming two of its groups, as the form sends them without a
  // choice: view and cat, not named, show value=, where any value in the query emptied them;
  // emptied and spot, named and not in the query, have nothing chosen.

  $_GET ['page']     = '2';
  $_GET ['padLists'] = [ 'emptied', 'spot' ];

?>
