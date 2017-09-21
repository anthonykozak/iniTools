#!/bin/sh
php process "_www.conf" "test/www.conf" -d -cr "-^(;|#)-" -sort