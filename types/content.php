<?php

  if ( isset ( $padContentStore [ $padTag [$pad] ] ) )
    return $padContentStore [ $padTag [$pad] ];

  if ( padContentCheck ( $padTag [$pad]  ) )
    return padContentData ( $padTag [$pad] );

  return NULL;

?>