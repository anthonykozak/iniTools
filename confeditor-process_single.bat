@echo off

chcp 65001

call "D:\HUBIC\LOGS\EasyPHP-Devserver-17\eds-binaries\php\php5630vc11x86x170819170605\php.exe" -c "D:\HUBIC\LOGS\EasyPHP-Devserver-17\eds-binaries\php\php5630vc11x86x170819170605\php.ini" -q ./confeditor.php process "_www.conf" "test/www.conf" -d -cr "-^(;|#)-" -sort
pause;