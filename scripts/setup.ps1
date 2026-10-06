$ErrorActionPreference = 'Stop'
if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }
if (-not (Test-Path 'backend/.env')) { Copy-Item 'backend/.env.example' 'backend/.env' }
if (-not (Test-Path 'frontend/.env')) { Copy-Item 'frontend/.env.example' 'frontend/.env' }
docker compose build
docker compose up -d postgres redis mailpit
$appKey = (docker compose run --rm api php artisan key:generate --show | Select-Object -Last 1).Trim()
if (-not $appKey.StartsWith('base64:')) { throw 'No se pudo generar APP_KEY.' }
(Get-Content 'backend/.env') -replace '^APP_KEY=.*$', "APP_KEY=$appKey" | Set-Content 'backend/.env'
docker compose run --rm api php artisan migrate --seed
docker compose up -d
Write-Host 'SupportFlow: http://localhost:5173 | API: http://localhost:8000 | Mailpit: http://localhost:8025'

