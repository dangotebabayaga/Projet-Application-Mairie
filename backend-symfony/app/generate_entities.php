#!/usr/bin/env php
<?php
/**
 * SCRIPT : Générateur automatique d'entités Symfony depuis BD_UniCity
 *
 * Usage: php generate_entities.php
 *
 * Ce script lit la structure de la base de données PostgreSQL
 * et génère automatiquement les classes d'entités Symfony.
 */

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())->bootEnv(__DIR__.'/.env');

// Configuration BDD
$host = 'db';
$port = '5432';
$dbname = 'BD_UniCity';
$user = 'user';
$password = 'pass';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ Connexion à BD_UniCity réussie\n\n";
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage() . "\n");
}

// Récupérer toutes les tables
$stmt = $pdo->query("
    SELECT table_name
    FROM information_schema.tables
    WHERE table_schema = 'public'
    AND table_type = 'BASE TABLE'
    ORDER BY table_name
");

$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "📊 Tables trouvées : " . count($tables) . "\n";
foreach ($tables as $table) {
    echo "  - $table\n";
}

echo "\n🔧 Génération des entités...\n\n";

// Fonction pour convertir snake_case en PascalCase
function toPascalCase($string) {
    return str_replace('_', '', ucwords($string, '_'));
}

// Fonction pour convertir snake_case en camelCase
function toCamelCase($string) {
    return lcfirst(toPascalCase($string));
}

// Mapper les types PostgreSQL vers Doctrine
function mapType($pgType, $length = null) {
    $typeMap = [
        'integer' => 'integer',
        'bigint' => 'bigint',
        'smallint' => 'smallint',
        'boolean' => 'boolean',
        'character varying' => 'string',
        'varchar' => 'string',
        'text' => 'text',
        'timestamp without time zone' => 'datetime',
        'timestamp with time zone' => 'datetime',
        'date' => 'date',
        'numeric' => 'decimal',
        'real' => 'float',
        'double precision' => 'float',
    ];

    return $typeMap[$pgType] ?? 'string';
}

// Générer les entités
$entitiesDir = __DIR__ . '/src/Entity';
if (!is_dir($entitiesDir)) {
    mkdir($entitiesDir, 0755, true);
}

foreach ($tables as $tableName) {
    $entityName = toPascalCase(rtrim($tableName, 's')); // Enlever le 's' du pluriel

    // Cas spéciaux
    if ($tableName === 'Utilisateur') $entityName = 'Utilisateur';
    if ($tableName === 'reseaux_sociaux') $entityName = 'ReseauSocial';
    if ($tableName === 'logs_actions') $entityName = 'LogAction';

    echo "  📝 Génération de $entityName (table: $tableName)...\n";

    // Récupérer les colonnes
    $stmt = $pdo->prepare("
        SELECT
            column_name,
            data_type,
            character_maximum_length,
            is_nullable,
            column_default
        FROM information_schema.columns
        WHERE table_name = :table
        ORDER BY ordinal_position
    ");
    $stmt->execute(['table' => $tableName]);
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Générer le contenu de la classe
    $entityContent = "<?php\n\nnamespace App\\Entity;\n\n";
    $entityContent .= "use App\\Repository\\" . $entityName . "Repository;\n";
    $entityContent .= "use Doctrine\\DBAL\\Types\\Types;\n";
    $entityContent .= "use Doctrine\\ORM\\Mapping as ORM;\n\n";
    $entityContent .= "#[ORM\\Entity(repositoryClass: " . $entityName . "Repository::class)]\n";
    $entityContent .= "#[ORM\\Table(name: '$tableName')]\n";
    $entityContent .= "class $entityName\n{\n";

    foreach ($columns as $column) {
        $colName = $column['column_name'];
        $propertyName = toCamelCase($colName);
        $type = mapType($column['data_type'], $column['character_maximum_length']);
        $length = $column['character_maximum_length'];
        $nullable = $column['is_nullable'] === 'YES';

        // ID
        if ($colName === 'id') {
            $entityContent .= "    #[ORM\\Id]\n";
            $entityContent .= "    #[ORM\\GeneratedValue]\n";
            $entityContent .= "    #[ORM\\Column]\n";
            $entityContent .= "    private ?int \$id = null;\n\n";
            continue;
        }

        // Colonnes normales
        $columnAnnotation = "    #[ORM\\Column(";

        if ($colName !== $propertyName) {
            $columnAnnotation .= "name: '$colName', ";
        }

        if ($type === 'text') {
            $columnAnnotation .= "type: Types::TEXT";
        } elseif ($type === 'datetime') {
            $columnAnnotation .= "type: Types::DATETIME_MUTABLE";
        } elseif ($type === 'date') {
            $columnAnnotation .= "type: Types::DATE_MUTABLE";
        } elseif ($type === 'decimal') {
            $columnAnnotation .= "type: Types::DECIMAL, precision: 10, scale: 2";
        } elseif ($type === 'string' && $length) {
            $columnAnnotation .= "length: $length";
        } else {
            $columnAnnotation .= "type: '" . $type . "'";
        }

        if ($nullable) {
            $columnAnnotation .= ", nullable: true";
        }

        $columnAnnotation .= ")]\n";

        $entityContent .= $columnAnnotation;

        $phpType = match($type) {
            'integer', 'bigint', 'smallint' => 'int',
            'boolean' => 'bool',
            'datetime', 'date' => '\\DateTimeInterface',
            'decimal', 'float' => 'float',
            default => 'string'
        };

        // Toujours rendre nullable pour PHP 8
        $phpType = '?' . $phpType;

        $entityContent .= "    private $phpType \$$propertyName = null;\n\n";
    }

    // Getters et Setters
    foreach ($columns as $column) {
        $colName = $column['column_name'];
        $propertyName = toCamelCase($colName);
        $type = mapType($column['data_type']);
        $nullable = $column['is_nullable'] === 'YES';

        $phpType = match($type) {
            'integer', 'bigint', 'smallint' => 'int',
            'boolean' => 'bool',
            'datetime', 'date' => '\\DateTimeInterface',
            'decimal', 'float' => 'float',
            default => 'string'
        };

        if ($colName === 'id') {
            $entityContent .= "    public function getId(): ?int\n";
            $entityContent .= "    {\n";
            $entityContent .= "        return \$this->id;\n";
            $entityContent .= "    }\n\n";
            continue;
        }

        // Getter
        $getterName = 'get' . ucfirst($propertyName);
        if ($type === 'boolean') {
            $getterName = 'is' . ucfirst($propertyName);
        }

        $returnType = $nullable || in_array($type, ['datetime', 'date']) ? '?' . $phpType : $phpType;

        $entityContent .= "    public function $getterName(): $returnType\n";
        $entityContent .= "    {\n";
        $entityContent .= "        return \$this->$propertyName;\n";
        $entityContent .= "    }\n\n";

        // Setter
        $setterParam = $nullable ? '?' . $phpType : $phpType;
        $entityContent .= "    public function set" . ucfirst($propertyName) . "($setterParam \$$propertyName): static\n";
        $entityContent .= "    {\n";
        $entityContent .= "        \$this->$propertyName = \$$propertyName;\n";
        $entityContent .= "        return \$this;\n";
        $entityContent .= "    }\n\n";
    }

    $entityContent .= "}\n";

    // Sauvegarder le fichier
    $filename = $entitiesDir . '/' . $entityName . '.php';
    file_put_contents($filename, $entityContent);

    echo "    ✅ $entityName.php créé\n";
}

echo "\n🎉 Terminé ! " . count($tables) . " entités générées dans src/Entity/\n";
echo "\n📝 Prochaines étapes :\n";
echo "  1. Vérifier les entités : php bin/console doctrine:mapping:info\n";
echo "  2. Ajouter les relations manuellement dans les entités\n";
echo "  3. Valider le schéma : php bin/console doctrine:schema:validate\n";
