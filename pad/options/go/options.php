<?php

  // Drives one phase of the option walk; the caller - go/start.php, go/end.php or go/app.php -
  // sets $padOptions to the phase name.
  //
  // The phase decides both which options may run and which text they work on: 'start' and 'app'
  // change $padBase [$pad] before the content is generated, 'end' changes $padResult [$pad]
  // after it. The 'app' list is $padOptionsAppStart [$pad], the options the application itself
  // implements in an _options/ directory; any other phase resolves the constant
  // padOptions<Phase> - padOptionsStart and padOptionsEnd, see inits/const.php.
  //
  // The tag's options are then walked in the order they were parsed, skipping any already
  // consumed elsewhere (padTagParm marks an option done), and each handler is called through
  // call/any.php with $padGetName holding the option value. call/any.php silently ignores a
  // handler file that does not exist, which is how listed options that are implemented
  // elsewhere - sort, dedup and page in handling/, track and trace in info/ - pass through.

  if     ( $padOptions == 'app' ) $padOptionsWalk = $padOptionsAppStart [$pad];
  else                            $padOptionsWalk = constant ( 'padOptions' . ucfirst($padOptions) );

  if     ( $padOptions == 'start'    ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'      ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'      ) $padContent = $padBase   [$pad];

  foreach ( $padPrm [$pad] as $padOptionName => $padV )

    if ( in_array ( $padOptionName, $padOptionsWalk ) and ! padIsDone ( $padOptionName ) ) {

      $padGetName = padTagParm ( $padOptionName, '???' );

      padDone ( $padOptionName );

      if ( $padOptions == 'app' )
        $padCall = $padOptionsAppStartCall [$pad] [$padOptionName] ;
      else
        $padCall = PAD . "options/$padOptionName.php" ;

      include PAD . 'call/any.php';

    }

  if     ( $padOptions == 'start'    ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'      ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'      ) $padBase   [$pad] = $padContent;

?>