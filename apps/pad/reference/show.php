<?php
  
  $showTitle = ( isset ($skipTitle) ) ? FALSE : TRUE;
  
  $target = padApp . $item;

  if ( padIsDir ($target) and ! padExists ("$target/index.php") and ! padExists ("$target/index.html") ) {

    if ( padExists ( "$target/.settings.php" ) )
      include  "$target/.settings.php";
 
    $dir = $item;
    
    $show = dirList ($dir);

    foreach ($show as $key => $value)
      $show [$key] ['onlyResult'] = onlyResult ( padApp . $value ['item'] . '.html' );

  } else { 

    if ( padExists ( "$target.settings.php" ) )
      include  "$target.settings.php";
 
    $show = [];

    $item = padPageGetName ($item);

    $go = go ( $item );

    if ( $go )
      padRedirect ( "reference/show&item=$go" );
    
    $onlyResult = onlyResult ( padApp . $item . '.html' );

  }

?>