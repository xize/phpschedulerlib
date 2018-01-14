@echo off
cls
title syncing to xampp....
del C:\xampp\htdocs /r /f
xcopy example\* C:\xampp\htdocs\test /i /s /d /y
xcopy src\* C:\xampp\htdocs /i /s /d /y