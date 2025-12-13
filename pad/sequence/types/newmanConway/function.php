<?php


/**
 * Calculates the nth Newman-Conway sequence term.
 *
 * Recursive sequence where P(n) = P(P(n-1)) + P(n-P(n-1)).
 * Uses memoization in $GLOBALS['pqCache'] for performance.
 *
 * @param int $n The index in the sequence.
 *
 * @return int The nth Newman-Conway number.
 */
function pqNewmanConway ($n) {

	if ($n == 1 || $n == 2)
		return 1;

	if ( ! isset ( $GLOBALS ['pqCache'] ) )
	  $GLOBALS ['pqCache'] = [];

	if ( isset ( $GLOBALS ['pqCache'] [$n] ) )
		return $GLOBALS ['pqCache'] [$n];

	$now = pqNewmanConway ( pqNewmanConway ($n - 1)     )
	       +
		   pqNewmanConway ( $n - pqNewmanConway($n - 1) );

	$GLOBALS ['pqCache'] [$n] = $now;

	return $now;

}

// This code is contributed by Ajit.
?>