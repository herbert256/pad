<?php

  // Last chance to place the leftover first parameter, once the sequence, the action and the
  // pull have all been resolved and both a type and an action may be in play.
  //
  // A type that declares flags/parm - one that genuinely takes an argument, such as multiply
  // or repeat - claims it first; then the action, when it is the action's kind of parameter
  // (parm/kind.php); what neither takes is named by parm/none.php. An action of actions/double/
  // or merge/ took any parameter, a store or not: {reverse 'abc'} and {append 'abc'} reversed
  // and appended nothing, without a word.

  if ( ! is_array ( $pqFindParm ) and (string) $pqFindParm === '' )
    return;

  $pqFindKind = FALSE;

  if ( $pqAction )
    include PQ . 'inits/find/parm/kind.php';

  if ( ! $pqFindKind )
    include PQ . 'inits/find/parm/store.php';

  if ( ! is_array ( $pqFindParm ) and (string) $pqFindParm === '' )
    return;

  // Beside a store pulled with neither, a name is named too: {pull:s 'abc'} and {pull:s reverse,
  // 'abc'} dropped it without a word. A number there is left as it was, the parameter of the
  // type the tag plays with - {make add=3, mySequence, 3} - parm/inits.php.

      if ( $pqSeq    and padFileExists ( PT . "$pqSeq/flags/parm")  ) $pqParm       = $pqFindParm;
  elseif ( $pqFindKind and is_array ( $pqFindParm )                  ) $pqActionParm = pqActionList ( $pqAction, $pqFindParm ) ?? '';
  elseif ( $pqFindKind                                              ) $pqActionParm = $pqFindParm;
  elseif ( $pqSeq or $pqAction                                      ) include PQ . 'inits/find/parm/none.php';
  elseif ( $pqPull and ! is_numeric ( $pqFindParm )                 ) include PQ . 'inits/find/parm/none.php';
  else                                                                return;

  // Placed, it is no longer left over - parm/second.php reads what is.

  $pqFindParm = '';

?>
