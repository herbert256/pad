<?php

  // Nested passes: {code} and {sandbox} with their reset, clean and sandbox options.
  //
  // Each case is the page as check renders it, with the answer it is supposed to give stated
  // here instead of left to a stored copy of the HTML.

  return [

    [ 'start/code/increment/base',
      <<<'PAD'
      {increment $abc} {$abc}
      {code}
        {increment $abc} {$abc}
        {code}
          {increment $abc} {$abc}
          {code}
            {increment $abc} {$abc}
            {code}
              {increment $abc} {$abc}
              {code}
                {increment $abc} {$abc}
                {code}
                  {increment $abc} {$abc}
                  {code}
                    {increment $abc} {$abc}
                    {code}
                      {increment $abc} {$abc}
                    {/code} {$abc}
                  {/code} {$abc}
                {/code} {$abc}
              {/code}{$abc}
            {/code} {$abc}
          {/code} {$abc}
        {/code} {$abc}
      {/code}{$abc}
      PAD,
      ' 12345678999999999' ],

    [ 'start/code/set/base',
      <<<'PAD'
      {set $abc = 1} {$abc}
      {code}
        {set $abc = 2} {$abc}
        {code}
          {set $abc = 3} {$abc}
          {code}
            {set $abc = 4} {$abc}
            {code}
              {set $abc = 5} {$abc}
              {code}
                {set $abc = 6} {$abc}
                {code}
                  {set $abc = 7} {$abc}
                  {code}
                    {set $abc = 8} {$abc}
                    {code}
                      {set $abc = 9} {$abc}
                    {/code} {$abc}
                  {/code} {$abc}
                {/code} {$abc}
              {/code}{$abc}
            {/code} {$abc}
          {/code} {$abc}
        {/code} {$abc}
      {/code}{$abc}
      PAD,
      ' 12345678999999999' ],

    [ 'start/combi1/page9',
      <<<'PAD'
      {code}
      
        {increment $abc}
      
        {$abc}
      
      {/code}
      PAD,
      '1' ],

    [ 'start/combi3/page9',
      <<<'PAD'
      {code}
      
        {increment $abc}
      
        {$abc}
      
      {/code}
      PAD,
      '1' ],

    [ 'start/page/1/page9',
      <<<'PAD'
      {increment $abc}
      
      {$abc}
      PAD,
      '1' ],

    [ 'start/page/2/page9',
      <<<'PAD'
      {set $abc=9}
      
      {$abc}
      PAD,
      '9' ],

    [ 'start/page/3/page9',
      <<<'PAD'
      {set $abc=9}
      
      {$abc}
      PAD,
      '9' ],

    [ 'start/page/4/page9',
      <<<'PAD'
      {set $abc=9}
      
      {$abc}
      PAD,
      '9' ],

    [ 'start/page/5/page9',
      <<<'PAD'
      {increment $abc} 
      
      {$abc}
      PAD,
      ' 1' ],

    [ 'start/page/6/page9',
      <<<'PAD'
      {increment $abc} 
      
      {$abc}
      PAD,
      ' 1' ],

    [ 'start/page/7/page9',
      <<<'PAD'
      {increment $abc} 
      
      {$abc}
      PAD,
      ' 1' ],

    [ 'start/page/8/page9',
      <<<'PAD'
      {increment $abc} 
      
      {$abc}
      PAD,
      ' 1' ],

  ];

?>