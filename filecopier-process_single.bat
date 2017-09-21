@echo off

chcp 65001

call "D:\HUBIC\LOGS\EasyPHP-Devserver-17\eds-binaries\php\php5630vc11x86x170819170605\php.exe" -c "D:\HUBIC\LOGS\EasyPHP-Devserver-17\eds-binaries\php\php5630vc11x86x170819170605\php.ini" -q ./filecopier.php process /etc/nginx/nginx.conf -b -d
pause;