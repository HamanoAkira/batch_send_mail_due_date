@echo off

cd /d "C:\Users\OS\Documents\project\batch\src"
set current_date=%DATE:~10,4%-%DATE:~4,2%-%DATE:~7,2%

php index.php %current_date% 3 >> "C:\Users\OS\Documents\project\batch\log.txt"
echo. >> "C:\Users\OS\Documents\project\batch\log.txt"
