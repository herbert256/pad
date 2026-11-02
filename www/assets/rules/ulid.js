addRule('ulid', { check: function(value) { return /^(?:[0-7][0-9A-HJKMNP-TV-Z]{25})$(?![\s\S])/.test(value); } });
