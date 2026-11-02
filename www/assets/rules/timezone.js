addRule('timezone', { check: function(value, argument) { try { return JSON.parse(argument).indexOf(value) !== -1; } catch (e) { return false; } } });
