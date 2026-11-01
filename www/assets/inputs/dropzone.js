/* dropzone - a file dropped on the frame becomes the file field's file, as one chosen does:
   the files the field takes - by its accept= and the size limit= - are put in the field
   (a DataTransfer of them), one alone unless it takes multiple, and every file refused is
   said with why. The files chosen are listed with their sizes. The frame is marked while a
   file is dragged over it. */
PadInputs.register('dropzone', function (frame, cfg, kit) {
  var field = frame.querySelector('input[type=file]');
  var zone = frame.querySelector('.pi-drop');
  var list = frame.querySelector('.pi-drop-files');
  var said = frame.querySelector('.pi-drop-said');
  if (!field || !zone) return;

  function size(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1).replace(/\.0$/, '') + ' MB';
    if (bytes >= 1024) return Math.round(bytes / 1024) + ' KB';
    return bytes + ' bytes';
  }

  // Whether the field's accept= takes the file: a type (image/png), a group (image/*) or an
  // extension (.pdf), as the file chooser reads it.
  function accepted(file) {
    var accept = (field.getAttribute('accept') || '').split(',').map(function (one) { return one.trim().toLowerCase(); }).filter(Boolean);
    if (!accept.length) return true;
    var name = file.name.toLowerCase(), type = (file.type || '').toLowerCase();
    return accept.some(function (one) {
      if (one.charAt(0) === '.') return name.slice(-one.length) === one;
      if (one.slice(-2) === '/*') return type.indexOf(one.slice(0, -1)) === 0;
      return type === one;
    });
  }

  function show() {
    list.textContent = '';
    Array.prototype.forEach.call(field.files || [], function (file) {
      var li = kit.el('li', 'pi-drop-file');
      li.appendChild(kit.el('span', 'pi-drop-name', file.name));
      li.appendChild(kit.el('span', 'pi-muted', size(file.size)));
      list.appendChild(li);
    });
    list.hidden = !(field.files && field.files.length);
  }

  function take(files) {
    var kept = [], refused = [];
    Array.prototype.forEach.call(files, function (file) {
      if (!accepted(file)) refused.push(file.name + ' is not a file this field takes');
      else if (cfg.limit && file.size > cfg.limit) refused.push(file.name + ' is larger than ' + cfg.size);
      else kept.push(file);
    });
    if (!field.multiple && kept.length > 1) {
      refused.push('one file only - ' + kept[0].name + ' is kept');
      kept = kept.slice(0, 1);
    }
    if (kept.length) {
      try {
        var transfer = new DataTransfer();
        kept.forEach(function (file) { transfer.items.add(file); });
        field.files = transfer.files;
      } catch (e) {
        refused.push('this browser cannot take a dropped file - click to choose it');
      }
      kit.changed(field);
    }
    if (said) said.textContent = refused.join('; ');
    show();
  }

  ['dragenter', 'dragover'].forEach(function (type) {
    zone.addEventListener(type, function (event) {
      if (!event.dataTransfer || Array.prototype.indexOf.call(event.dataTransfer.types || [], 'Files') < 0) return;
      event.preventDefault();
      event.dataTransfer.dropEffect = 'copy';
      zone.classList.add('is-over');
    });
  });
  ['dragleave', 'dragend'].forEach(function (type) {
    zone.addEventListener(type, function (event) {
      if (!zone.contains(event.relatedTarget)) zone.classList.remove('is-over');
    });
  });
  zone.addEventListener('drop', function (event) {
    event.preventDefault();
    zone.classList.remove('is-over');
    if (event.dataTransfer && event.dataTransfer.files.length) take(event.dataTransfer.files);
  });

  field.addEventListener('change', function () {
    var refused = [];
    if (cfg.limit)
      Array.prototype.forEach.call(field.files || [], function (file) { if (file.size > cfg.limit) refused.push(file.name + ' is larger than ' + cfg.size); });
    if (refused.length) field.value = '';
    if (said) said.textContent = refused.join('; ');
    show();
  });

  frame.classList.add('is-droppable');
  show();
});
