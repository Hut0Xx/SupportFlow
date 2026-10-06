$ErrorActionPreference = 'Stop'
pnpm check
docker compose run --rm api ./vendor/bin/pint --test
$dbUserLine = Get-Content '.env' | Where-Object { $_ -match '^DB_USERNAME=' } | Select-Object -First 1
$dbUser = if ($dbUserLine) { ($dbUserLine -split '=', 2)[1] } else { 'supportflow' }
docker compose exec -T postgres createdb -U $dbUser supportflow_test
if ($LASTEXITCODE -ne 0) { Write-Host 'La base supportflow_test ya existe; se reutilizará.' }
docker compose run --rm -e DB_DATABASE=supportflow_test api php artisan test

