<?php

  // Drives one phase of the option walk; the caller - go/start.php, go/end.php, go/app.php or
  // go/appEnd.php - sets $padOptions to the phase name.
  //
  // The phase decides both which options may run and which text they work on: 'start' and 'app'
  // change $padBase [$pad] before the content is generated, 'end' and 'appEnd' change
  // $padResult [$pad] after it. The 'app' list is $padOptionsAppStart [$pad], the options the
  // application itself implements in an _options/ directory, and 'appEnd' the ones it
  // implements in _options/end/; any other phase resolves the constant padOptions<Phase> -
  // padOptionsStart and padOptionsEnd, see inits/const.php.
  //
  // An option with both an _options/ and an _options/end/ handler is marked done by the first
  // and still has its second: the end walk passes over only what something other than the
  // application's own start handler consumed.
  //
  // The tag's options are then walked in the order they were parsed, skipping any already
  // consumed elsewhere (padTagParm marks an option done), and each handler is called through
  // call/any.php with $padGetName holding the option value. call/any.php silently ignores a
  // handler file that does not exist, which is how listed options that are implemented
  // elsewhere - sort, dedup and page in handling/, track and trace in info/ - pass through.

  if     ( $padOptions == 'app'    ) $padOptionsWalk = $padOptionsAppStart [$pad];
  elseif ( $padOptions == 'appEnd' ) $padOptionsWalk = $padOptionsAppEnd   [$pad];
  else                               $padOptionsWalk = constant ( 'padOptions' . ucfirst($padOptions) );

  if     ( $padOptions == 'start'    ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'end'      ) $padContent = $padResult [$pad];
  elseif ( $padOptions == 'app'      ) $padContent = $padBase   [$pad];
  elseif ( $padOptions == 'appEnd'   ) $padContent = $padResult [$pad];

  // The storing options - toBool, toContent, toData - each take the text and leave none
  // behind; the first keeps what it took here, so the next one written on the tag reads the
  // same text: toContent then toBool stored a flag of FALSE, toBool then toContent nothing.

  $padStoreText = NULL;

  // The start options run in the order the phase lists them - ignore before print - whatever
  // the order they were written in: ignore wraps the text it finds, and run after print it
  // wrapped print's {&firstFieldValue} too, so {xs print, ignore} printed that text for every
  // row. The other phases keep the written order - tidy, toContent stores the tidied text.

  $padOptionsNames = ( $padOptions == 'start' ) ? array_intersect ( $padOptionsWalk, array_keys ( $padPrm [$pad] ) )
                                                : array_keys ( $padPrm [$pad] );

  foreach ( $padOptionsNames as $padOptionName )

    if ( in_array ( $padOptionName, $padOptionsWalk )
         and ( ! padIsDone ( $padOptionName )
               or ( $padOptions == 'appEnd' and in_array ( $padOptionName, $padOptionsAppStart [$pad] ) ) ) ) {

      $padGetName = padTagParm ( $padOptionName, '???' );

      padDone ( $padOptionName );

      if     ( $padOptions == 'app'    ) $padCall = $padOptionsAppStartCall [$pad] [$padOptionName] ;
      elseif ( $padOptions == 'appEnd' ) $padCall = $padOptionsAppEndCall   [$pad] [$padOptionName] ;
      else                               $padCall = PAD . "options/$padOptionName.php" ;

      include PAD . 'call/any.php';

    }

  if     ( $padOptions == 'start'    ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'end'      ) $padResult [$pad] = $padContent;
  elseif ( $padOptions == 'app'      ) $padBase   [$pad] = $padContent;
  elseif ( $padOptions == 'appEnd'   ) $padResult [$pad] = $padContent;

?>
