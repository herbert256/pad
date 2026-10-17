<?php

  // A calendar whose parameter holds the month the request asked for - {calendar $month},
  // the request value its links set - keeps the links to the months around it: following
  // them changes the month it shows. They were dropped with every parameter, and such a
  // calendar could no longer leave November.

  $_GET ['month'] = '2026-11';

  $month = padRequest ( 'month', padNow ( 'Y-m' ) );

?>
