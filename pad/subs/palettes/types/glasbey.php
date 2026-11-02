<?php

  // glasbey - thirty-two colours to tell many categories apart, each chosen as far as it can be
  // from all before it, by the method of Chris Glasbey, Gerie van der Heijden, Vivian Toh and
  // others ("Colour displays for categorical images", Color Research and Application 32.4, 2007):
  // started from white and black, so none is near either, and no grey among them, every colour of
  // a chroma of 20 or more. Past eight the colours are for a map or a legend more than a chart,
  // which has eight slots, and colour-blind eyes will confuse some of them - for them okabe-ito or
  // a Tol palette. The first 32 of the 256 colours of colorcet's glasbey (glasbey_bw_minc_20,
  // colorcet/__init__.py, HoloViz), each channel k/255, unchanged but for the cut; colorcet,
  // CC BY 4.0 licence.

  return [ 'name'   => 'Glasbey',
           'type'   => 'categorical',
           'colors' => [ '#d70000', '#8c3cff', '#028800', '#00acc7', '#98ff00', '#ff7fd1', '#6c004f', '#ffa530',
                         '#583b00', '#005759', '#0000dd', '#00fdcf', '#a1756a', '#bcb7ff', '#95b578', '#c004b9',
                         '#645474', '#790000', '#0774d8', '#fef590', '#004b00', '#8f7a00', '#ff7266', '#eeb9b9',
                         '#5e7e66', '#9be4ff', '#ec0077', '#a67bb9', '#5a00a4', '#04c600', '#9e4b00', '#9c3b50' ] ];

?>
