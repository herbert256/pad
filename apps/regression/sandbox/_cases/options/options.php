<?php

  // Tag options, each against the same tag without it.
  //
  // An option is only worth a case where its presence changes the answer, so most of these are
  // written as a pair: glue with and without print, an unknown tag with and without optional.
  //
  // open, close and glue only reach the output through print, which is why each is written with
  // it. The last two also pin down what an unknown tag leaves behind, which is not the same for
  // both spellings - a pair and a single tag both keep their name.

  return [

    [ 'print lists the store',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs print='xs'/}
      PAD,
      '123' ],

    [ 'glue separates them',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs print='xs', glue='-'/}
      PAD,
      '1-2-3' ],

    [ 'glue does nothing without print',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs glue='-'}
        {$xs}
      {/xs}
      PAD,
      '123' ],

    [ 'open goes in front',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs print='xs', open='['/}
      PAD,
      '[123' ],

    [ 'close goes after',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs print='xs', close=']'/}
      PAD,
      '123]' ],

    [ 'open and close together',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs print='xs', open='[', close=']', glue=','/}
      PAD,
      '[1,2,3]' ],

    [ 'ignore leaves the content unparsed',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs ignore}
        {$xs},
      {/xs}
      PAD,
      '{$xs},{$xs},{$xs},' ],

    [ 'toContent stores what was rendered',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs toContent='k'}
        {$xs},
      {/xs}
      {content:k}
      PAD,
      '1,2,3,' ],

    [ 'demand renders as usual when there is data',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs demand}
        {$xs},
      {/xs}
      PAD,
      '1,2,3,' ],

    [ 'merge bottom keeps the order',
      <<<'PAD'
      {data 'xs'}
        [1,2,3]
      {/data}
      {xs merge='bottom'}
        {$xs},
      {/xs}
      PAD,
      '1,2,3,' ],

    [ 'optional keeps an unknown tag quiet',
      <<<'PAD'
      {nosuch optional}
        x
      {/nosuch}
      PAD,
      '' ],

    [ 'an unknown pair keeps its name',
      <<<'PAD'
      {nosuch}
        x
      {/nosuch}
      PAD,
      '{nosuch}x{/nosuch}' ],

    [ 'noError swallows an unknown tag, the way optional does',
      <<<'PAD'
      {nosuch noError}
        x
      {/nosuch}
      PAD,
      '' ],

    [ 'an empty store renders nothing',
      <<<'PAD'
      {data 'e'}
        []
      {/data}
      {e}
        x
      {/e}
      PAD,
      '' ],

  ];

?>