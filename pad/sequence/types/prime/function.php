<?php

  function pqPrime (int $number) {

    if ($number==0) return false;
    $n=2;
    $primes=array();
    while(true){
        for ($i=0;$i<count($primes);$i++) {
            if ($n % $primes[$i] == 0) break;
        }
        if ($i==count($primes)) {
            array_push($primes, $n);
            if ($number == count($primes)) return $primes[count($primes)-1];
        }
        $n++;
    }
  
  }

?>