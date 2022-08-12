@echo off
start C:\wamp64\wampmanager.exe  
timeout /t 20  
start chrome --chrome --chrome-frame  --app="http://nidanreport.com/"
timeout /t 5
exit 
pause
