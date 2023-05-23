<?php

  $color = $color ?? 'black';
  
  $hi = 'Hello World !';
  
  echo <<< END
<h3>
  <font color="{$color}">
    {$hi}
  </font>
</h3>
END;

?>