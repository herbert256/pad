<?php

  switch ($pad_callback) {

    case 'init_tag':

        $salaryTotal = 0;
        $bonusTotal  = 0;
        
        break;

    case 'init_occurrence':

        $total = $salary + $bonus;
        
        $salaryTotal += $salary;
        $bonusTotal  += $bonus;
        
        break;

    case 'exit_occurrence':

        break;

    case 'exit_tag':

        $totalTotal = $salaryTotal + $bonusTotal ;

        break;

  }

?>