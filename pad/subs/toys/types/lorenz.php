<?php
return padToy('lorenz','Lorenz attractor',[
 'sigma'=>padToyNumber('sigma',10,1,30),'rho'=>padToyNumber('rho',28,1,60),'beta'=>padToyNumber('beta',8/3,.1,8),
 'dt'=>padToyNumber('dt',.005,.001,.02),'trail'=>padToyInt('trail',3000,100,6000),
 'projection'=>padToyChoice('projection','xz',['xy','xz','yz'])]);
?>
