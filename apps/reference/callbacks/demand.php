<?php

  switch ($pad_callback) {

    case 'init':

        $salaryTotal = 0;
        $bonusTotal  = 0;
        
        break;

    case 'row':

        $total = $salary + $bonus;
        
        $salaryTotal += $salary;
        $bonusTotal  += $bonus;
        
        break;

    case 'exit':

        $totalTotal = $salaryTotal + $bonusTotal ;

        break;

  }

?>