@echo off
setlocal enabledelayedexpansion

REM Update style.css
echo Updating style.css...
powershell -Command "(Get-Content 'css\style.css') -replace '17\.1875rem', '22rem' | Set-Content 'css\style.css'"

REM Check for updates in skin.css
echo Checking skin.css...
powershell -Command "if ((Get-Content 'css\skin.css') -match '17\.1875rem') { Write-Host 'Found in skin.css'; (Get-Content 'css\skin.css') -replace '17\.1875rem', '22rem' | Set-Content 'css\skin.css' } else { Write-Host 'No occurrences in skin.css' }"

REM Check for updates in skin-2.css
echo Checking skin-2.css...
powershell -Command "if ((Get-Content 'css\skin-2.css') -match '17\.1875rem') { Write-Host 'Found in skin-2.css'; (Get-Content 'css\skin-2.css') -replace '17\.1875rem', '22rem' | Set-Content 'css\skin-2.css' } else { Write-Host 'No occurrences in skin-2.css' }"

REM Check for updates in skin-3.css
echo Checking skin-3.css...
powershell -Command "if ((Get-Content 'css\skin-3.css') -match '17\.1875rem') { Write-Host 'Found in skin-3.css'; (Get-Content 'css\skin-3.css') -replace '17\.1875rem', '22rem' | Set-Content 'css\skin-3.css' } else { Write-Host 'No occurrences in skin-3.css' }"

echo Done!
