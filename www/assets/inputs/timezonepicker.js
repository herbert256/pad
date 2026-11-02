PadInputs.register('timezonepicker',function(frame,cfg,kit){
 var select=frame.querySelector('select');if(!select)return;var search=document.createElement('input');search.type='search';search.placeholder='Filter timezones';search.setAttribute('aria-label','Filter timezones');search.disabled=select.disabled;select.before(search);
 function filter(){var term=search.value.toLowerCase();Array.from(select.options).forEach(function(option){option.hidden=term!==''&&!option.textContent.toLowerCase().includes(term)&&!option.selected;});}
 search.addEventListener('input',filter);search.addEventListener('keydown',function(e){if(e.key==='ArrowDown'){e.preventDefault();select.focus();}if(e.key==='Escape'){search.value='';filter();}});
 if(select.form)select.form.addEventListener('reset',function(){search.value='';filter();});
});
