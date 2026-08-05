<?php

  // Action stage of the wind-down: applies the requested {action} transformations to
  // $pqResult. A thin alias so the exits pipeline names every stage; the work is in
  // actions/actions.php, which also records each action's output in $pqActionsHit.

  include PQ . 'actions/actions.php';

?>