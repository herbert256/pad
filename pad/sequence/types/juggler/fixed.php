<?php

	include_once 'include.php';

	$padSeq_juggler_array = [];

	if ( isset($padSeq_juggler) and $padSeq_juggler and is_numeric($padSeq_juggler) )

	  pSeq_juggler ($padSeq_juggler);

	elseif ( $padSeq_parm and is_numeric($padSeq_parm) )

	  pSeq_juggler ($padSeq_parm);

	elseif ( count ($padSeq_for) )
 
 	  pSeq_juggler ( reset($padSeq_parm) );

 	else 

 	  pSeq_juggler ( $padSeq_loop_start );

	return $padSeq_juggler_array;

?>