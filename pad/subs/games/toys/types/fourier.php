<?php
// fourier - sampled signal, harmonic spectrum and reconstruction error.
return padToy('fourier','Fourier signal inspector',[
 'signal'=>padToyChoice('signal','square',['sine','square','saw']),
 'harmonics'=>padToyInt('harmonics',8,1,32)]);
