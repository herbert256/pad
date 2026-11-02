<?php

  // auto - Selects a vertical or horizontal layout using the vertical= option.
  // Use this kind when the layout is supplied at runtime. Large lists stay vertical.
  return include PAD . 'subs/charts/timelines/types/' . ( padTagParm ( 'vertical', FALSE ) ? 'vertical' : 'horizontal' ) . '.php';

?>
