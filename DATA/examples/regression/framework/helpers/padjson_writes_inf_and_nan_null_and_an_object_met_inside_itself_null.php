<?php

  // padJson writes INF, -INF and NAN null, as the page's JSON answer does (padJsonFinite):
  // json_encode's partial output wrote them 0 - in a file padFilePut wrote, a dump, a trace
  // line. An object met again inside itself is null, as json_encode writes a recursion, and
  // a tree whose children point at their parent is walked once.

  $plain = str_replace ( [ "\n", ' ' ], '', padJson ( [ 'a' => INF, 'b' => [ -INF, NAN, 1.5, 0 ], 'c' => 'x' ] ) );

  $node       = new stdClass;
  $node->n    = INF;
  $node->self = $node;
  $cycle      = str_replace ( [ "\n", ' ' ], '', padJson ( $node ) );

  $root = new stdClass;
  $root->kids = [];
  $at = $root;
  for ( $i = 0; $i < 40; $i++ ) {
    $one = new stdClass;
    $one->parent = $at;
    $one->twin   = $at;
    $at->kids [] = $one;
    $at = $one;
  }
  $at->v = NAN;
  $start = microtime ( TRUE );
  $tree  = strlen ( padJson ( $root ) ) > 0 && microtime ( TRUE ) - $start < 5 ? 'quick' : 'slow';

  $none = padJson ( [ 'a' => 1, 'b' => [ 2.5 ] ] ) === json_encode ( [ 'a' => 1, 'b' => [ 2.5 ] ], JSON_PRETTY_PRINT ) ? 'same' : 'differs';

?>
