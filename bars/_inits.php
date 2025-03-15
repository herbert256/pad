<?php

  $envs = [ 'dev', 'tst', 'acc', 'prd' ];

  $versions ['dev'] = 5;
  $versions ['tst'] = 5;
  $versions ['acc'] = 6;
  $versions ['prd'] = 6;

  $all ['abc'] ['bars'] ['klm'] = $versions;
  $all ['abc'] ['bars'] ['xyz'] = $versions;
  $all ['xxx'] ['bars'] ['xyz'] = $versions;

  $wip = $all;

?>