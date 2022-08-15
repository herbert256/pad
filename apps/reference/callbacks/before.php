<?php

  switch ($padCallback) {

    case 'init':

        $salaryTotal = 0;
        $bonusTotal  = 0;
        
        break;

    case 'row':

        $row ['total'] = $row ['salary'] + $row ['bonus'];
        
        $salaryTotal += $row ['salary'];
        $bonusTotal  += $row ['bonus'];
        
        break;

    case 'exit':

        $totalTotal = $salaryTotal + $bonusTotal ;

        break;

  }

?>