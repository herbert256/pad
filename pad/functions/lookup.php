<?php

  // Pipe function lookup(set, key, field): the field of the row in another data set whose key
  // equals the value - {echo $customer_id | lookup('customers', 'id', 'name')} - a join for
  // two lists from different sources. key defaults to id; without a field the answer is the
  // row's first field other than the key; no matching row answers '', so a fallback reads
  // {echo $id | lookup('customers', 'id', 'name') | ?? 'unknown'}. The set is a {data}
  // block, an array of the page or a _data/ file, indexed once per request - padLookup in
  // lib/lookup.php.

  if ( ! $count or trim ( (string) $parm [0] ) === '' ) {

    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "lookup needs the data set to look in - lookup('customers', 'id', 'name')" );

    return '';

  }

  return padLookup ( $value, trim ( (string) $parm [0] ), trim ( (string) ( $parm [1] ?? 'id' ) ), trim ( (string) ( $parm [2] ?? '' ) ) );

?>
