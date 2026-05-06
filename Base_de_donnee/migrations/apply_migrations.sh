#!/bin/bash
set -e

DB_HOST="db"
DB_USER="unicity_user"
DB_NAME="unicity_db"
DB_PASSWORD="unicity_pass"

# correction : PGPASSWORD évite la demande interactive de mot de passe
export PGPASSWORD="$DB_PASSWORD"

echo "⏳ Waiting for PostgreSQL to be ready..."
until pg_isready -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME"; do
  echo "PostgreSQL not ready yet... retrying in 2s"
  sleep 2
done
echo "✅ PostgreSQL is ready!"

psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "
CREATE TABLE IF NOT EXISTS migrations (
  id SERIAL PRIMARY KEY,
  filename TEXT UNIQUE,
  executed_at TIMESTAMP DEFAULT NOW()
);
"

echo "📦 Applying migrations..."

for file in /migrations/*.sql; do
  filename=$(basename "$file")
  echo "➡️ Checking $filename"

  already_executed=$(psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -tAc "SELECT 1 FROM migrations WHERE filename='$filename'")

  if [ "$already_executed" != "1" ]; then
    echo "🚀 Applying $filename"
    psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -f "$file"
    psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c \
      "INSERT INTO migrations (filename) VALUES ('$filename');"
    echo "✅ $filename applied"
  else
    echo "⏭️ $filename already applied"
  fi
done

echo "🎉 All migrations processed!"