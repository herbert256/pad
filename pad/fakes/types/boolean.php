<?php

  // boolean - TRUE with a chance of chance= percent, 50 unless said otherwise (padFakeBool).
  // As a tag a FALSE is a miss: {fake 'boolean'}yes@else@no{/fake}.

  return padFakeBool ( padFakeOpt ( 'chance', 50 ) );

?>
