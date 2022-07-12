<?php

	include_once 'include.php';

	$pad_seq_juggler_array = [];

	if ( isset($pad_seq_juggler) and $pad_seq_juggler and is_numeric($pad_seq_juggler) )

	  pad_seq_juggler ($pad_seq_juggler);

	elseif ( $pad_seq_parm and is_numeric($pad_seq_parm) )

	  pad_seq_juggler ($pad_seq_parm);

	elseif ( count ($pad_seq_for) )
 
 	  pad_seq_juggler ( reset($pad_seq_parm) );

 	else 

 	  pad_seq_juggler ( $pad_seq_loop_start );

	return $pad_seq_juggler_array;

?>