<?php
// PHP code to generate first 'n' terms
// of the Moser-de Bruijn Seq

// Function to generate nth term
// of Moser-de Bruijn Seq
function padSeqMoserdebruijn($n)
{
  
   $S = array();
 
    $S[0] = 0;
    $S[1] = 1;
 
    for ( $i = 2; $i <= $n; $i++)
    {
        // S(2 * n) = 4 * S(n)
        if ($i % 2 == 0)
        $S[$i] = 4 * $S[$i / 2];
     
        // S(2 * n + 1) = 4 * S(n) + 1
        else
        $S[$i] = 4 * $S[ceil($i/2)] + 1;
    }
     
    return $S[$n];
}

// This code is contributed by anuj_67.

?>