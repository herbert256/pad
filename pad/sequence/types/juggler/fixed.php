<?php

	include_once 'include.php';

	$pSeq_juggler_array = [];

	if ( isset($pSeq_juggler) and $pSeq_juggler and is_numeric($pSeq_juggler) )

	  pSeq_juggler ($pSeq_juggler);

	elseif ( $pSeq_parm and is_numeric($pSeq_parm) )

	  pSeq_juggler ($pSeq_parm);

	elseif ( count ($pSeq_for) )
 
 	  pSeq_juggler ( reset($pSeq_parm) );

 	else 

 	  pSeq_juggler ( $pSeq_loop_start );

	return $pSeq_juggler_array;

?>