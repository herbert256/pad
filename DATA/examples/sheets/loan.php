<?php

  // The cells of the loan come from PHP, $loan, which its template reads with data=.

  $title = 'Loan';

  include APP . '_sheets/loan.php';

  sheetsShow ( 'loan' );

?>
