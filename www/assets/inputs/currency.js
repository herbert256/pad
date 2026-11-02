PadInputs.register('currency',function(frame,cfg){
 var input=frame.querySelector('input'), out=frame.querySelector('output'), format;
 try { format=new Intl.NumberFormat(cfg.locale,{style:'currency',currency:cfg.currency,minimumFractionDigits:cfg.decimals,maximumFractionDigits:cfg.decimals}); } catch(e) {}
 function show(){out.textContent=input.value!==''&&input.validity.valid&&Number.isFinite(input.valueAsNumber)&&format ? format.format(input.valueAsNumber):'';}
 input.addEventListener('input',show); if(input.form)input.form.addEventListener('reset',function(){setTimeout(show,0);}); show();
});
