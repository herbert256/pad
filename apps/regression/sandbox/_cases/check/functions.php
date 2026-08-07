<?php

  // Pipe functions carried over from check.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'functions/between',
      <<<'PAD'
      {if 10 range (20, 40)   } NOK @else@ ok  {/if}
      {if 20 range (20, 40)   } ok  @else@ NOK {/if}
      {if 30 range (20, 40)   } ok  @else@ NOK {/if}
      {if 40 range (20, 40)   } ok  @else@ NOK {/if}
      {if 50 range (20, 40)   } NOK @else@ ok  {/if}
      PAD,
      ' ok  ok  ok  ok  ok  ' ],

    [ 'functions/range',
      <<<'PAD'
      {if 10 range (20, 40)   } NOK @else@ ok  {/if}
      {if 20 range (20, 40)   } ok  @else@ NOK {/if}
      {if 30 range (20, 40)   } ok  @else@ NOK {/if}
      {if 40 range (20, 40)   } ok  @else@ NOK {/if}
      {if 50 range (20, 40)   } NOK @else@ ok  {/if}
      PAD,
      ' ok  ok  ok  ok  ok  ' ],

  ];

?>