<?php

  // Function build for prime: pqPrime($n) returns the nth prime - 2, 3, 5, 7, 11, ... -
  // found by trial division of each candidate against the primes collected so far.
  //
  // Not the default path, since pqBuild() prefers loop.php; reached with build=function and
  // called by prime/make.php. Loaded on every prime build anyway, because build/include.php
  // pulls in a type's function.php unconditionally.

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