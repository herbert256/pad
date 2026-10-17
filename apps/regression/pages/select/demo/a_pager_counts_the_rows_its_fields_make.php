<?php

  // A pager counts the rows the select answers also when its fields= decides what a row is:
  // distinct countries, or one row of totals. The count over the table alone was taken
  // whenever distinct=, group=, having= and union= were not given, so 27 countries had the
  // 13 pages of the 122 customers, and the one row of totals a pager of 28 pages.

  $padSelect ['countriesPaged'] = [ 'db' => 'customers', 'key' => 'customerNumber' ];
  $padSelect ['paymentTotals']  = [ 'db' => 'payments',  'key' => 'checkNumber' ];

?>
