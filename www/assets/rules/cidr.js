addRule('cidr',{check:function(value){var m=/^([^/]+)\/(0|[1-9][0-9]{0,2})$/.exec(value);return !!m&&((ipv4(m[1])&&+m[2]<=32)||(ipv6(m[1])&&+m[2]<=128));}});
