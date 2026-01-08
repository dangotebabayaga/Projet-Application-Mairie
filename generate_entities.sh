#!/bin/bash

set -e

if [ "$#" -ne 2 ]; then
  echo "Usage: $0 chemin/fichier.sql chemin/backend"
  exit 1
fi

SQL_FILE=$(realpath "$1")
BACKEND="$2"

if [ ! -f "$SQL_FILE" ]; then
  echo "❌ Fichier SQL introuvable : $SQL_FILE"
  exit 1
fi

if [ ! -d "$BACKEND" ]; then
  echo "❌ Backend introuvable : $BACKEND"
  exit 1
fi

cd "$BACKEND"
mkdir -p src/Entity

# Variables temporaires
TABLE_NAME=""
COLUMNS=()

while IFS= read -r line || [ -n "$line" ]; do
    line=$(echo "$line" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//') # trim

    # Début CREATE TABLE
    if [[ $line =~ ^CREATE[[:space:]]+TABLE[[:space:]]+\"?([a-zA-Z0-9_]+)\"? ]]; then
        TABLE_NAME="${BASH_REMATCH[1]}"
        COLUMNS=()
        continue
    fi

    # Fin de CREATE TABLE
    if [[ -n $TABLE_NAME && $line =~ \)\; ]]; then
        if [ -z "$TABLE_NAME" ] || [ ${#COLUMNS[@]} -eq 0 ]; then
            TABLE_NAME=""
            COLUMNS=()
            continue
        fi

        # Génération du fichier PHP
        CLASS_NAME=$(echo "$TABLE_NAME" | awk -F_ '{for(i=1;i<=NF;i++){$i=toupper(substr($i,1,1)) substr($i,2)}}1' | tr -d ' ')
        FILE="src/Entity/$CLASS_NAME.php"

        echo "<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '$TABLE_NAME')]
class $CLASS_NAME
{" > "$FILE"

        for COL in "${COLUMNS[@]}"; do
            NAME=$(echo "$COL" | cut -d'|' -f1)
            TYPE=$(echo "$COL" | cut -d'|' -f2)
            PRIMARY=$(echo "$COL" | cut -d'|' -f3)
            NULLABLE=$(echo "$COL" | cut -d'|' -f4)

            PROP=$(echo "$NAME" | awk -F_ '{for(i=1;i<=NF;i++){if(i==1){printf tolower($i)}else{printf toupper(substr($i,1,1)) substr($i,2)}}}')

            if [[ $PRIMARY == "1" ]]; then
                echo "    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int \$$PROP = null;" >> "$FILE"
            else
                echo "    #[ORM\Column(nullable: $NULLABLE)]
    private ?$TYPE \$$PROP = null;" >> "$FILE"
            fi
            echo "" >> "$FILE"
        done

        echo "}" >> "$FILE"
        echo "✅ $CLASS_NAME.php créé"
        TABLE_NAME=""
        COLUMNS=()
        continue
    fi

    # Lecture des colonnes
    if [ -n "$TABLE_NAME" ]; then
        # ignorer FOREIGN KEY / contraintes
        if [[ $line =~ ^ALTER[[:space:]]+TABLE ]]; then
            continue
        fi

        if [[ $line =~ ^\"?([a-zA-Z0-9_]+)\"?[[:space:]]+([a-zA-Z0-9]+).* ]]; then
            NAME="${BASH_REMATCH[1]}"
            SQLTYPE="${BASH_REMATCH[2]}"

            # Mapping SQL -> PHP
            case "$SQLTYPE" in
                *int*) TYPE="int";;
                varchar*|text|char*) TYPE="string";;
                timestamp|date|heure) TYPE="\\DateTimeInterface";;
                decimal|numeric|float|double) TYPE="float";;
                boolean|bool) TYPE="bool";;
                *) TYPE="string";;
            esac

            # PRIMARY KEY ou id
            if [[ "$line" =~ primary[[:space:]]key ]] || [[ "$NAME" == "id" ]]; then
                PRIMARY=1
                NULLABLE=false
            else
                PRIMARY=0
                if [[ $line =~ not[[:space:]]null ]]; then
                    NULLABLE=false
                else
                    NULLABLE=true
                fi
            fi

            COLUMNS+=("$NAME|$TYPE|$PRIMARY|$NULLABLE")
        fi
    fi

done < "$SQL_FILE"

echo "🎉 Terminé : toutes les entités générées dans src/Entity"
