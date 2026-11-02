<?php
  function padSolve ( $kind, $problem, $options = [] ) {
    include_once PAD . 'subs/constraints/_lib/constraints.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'constraints', $kind ) || ! is_array ( $problem ) || ! is_array ( $options ) )
        throw new InvalidArgumentException ( 'expected a known kind, problem and options arrays' );
      $definition = include PAD . "subs/constraints/types/$kind.php";
      foreach ( $options as $key => $value )
        if ( ! in_array ( $key, $definition ['options'], TRUE ) ) throw new InvalidArgumentException ( 'unknown option: ' . $key );
      $max = $options ['maxSteps'] ?? 100000;
      if ( ! is_int ( $max ) || $max < 1 || $max > 10000000 || ( isset ( $options ['cancel'] ) && ! is_callable ( $options ['cancel'] ) ) )
        throw new InvalidArgumentException ( 'maxSteps must be 1..10000000; cancel must be callable' );
      $state = [ 'steps' => 0, 'max' => $max, 'cancel' => $options ['cancel'] ?? NULL ];
      try { $result = $definition ['call'] ( $problem, $options, $state ); }
      catch ( PadSolveStop $stop ) { $result = [ 'status' => $stop->getMessage (), 'solution' => NULL ]; }
      return $result + [ 'steps' => $state ['steps'], 'maxSteps' => $max ];
    } catch ( InvalidArgumentException $e ) { padError ( 'padSolve: ' . $e->getMessage () ); return NULL; }
  }
?>
