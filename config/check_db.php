<?php
require_once 'database.php';

try {
    $pdo = getDBConnection();
    
    // Liste des tables requises
    $requiredTables = [
        'utilisateurs' => [
            'id',
            'nom',
            'prenom',
            'matricule',
            'role',
            'pool',
            'pm_id'
        ],
        'entretiens' => [
            'id',
            'utilisateur_id',
            'date_entretien',
            'type',
            'status'
        ],
        'conges' => [
            'id',
            'utilisateur_id',
            'jours_restants'
        ],
        'formations' => [
            'id',
            'utilisateur_id',
            'statut'
        ],
        'documents' => [
            'id',
            'utilisateur_id'
        ],
        'demandes' => [
            'id',
            'utilisateur_id',
            'statut'
        ]
    ];

    // Vérifier chaque table
    foreach ($requiredTables as $table => $columns) {
        // Vérifier si la table existe
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            echo "Table manquante: $table\n";
            
            // Créer la table avec les colonnes de base
            $sql = "CREATE TABLE $table (";
            $columnDefs = [];
            
            foreach ($columns as $column) {
                switch ($column) {
                    case 'id':
                        $columnDefs[] = "id INT AUTO_INCREMENT PRIMARY KEY";
                        break;
                    case 'utilisateur_id':
                        $columnDefs[] = "utilisateur_id INT";
                        break;
                    case 'date_entretien':
                        $columnDefs[] = "date_entretien DATETIME";
                        break;
                    case 'jours_restants':
                        $columnDefs[] = "jours_restants INT DEFAULT 0";
                        break;
                    case 'statut':
                        $columnDefs[] = "statut VARCHAR(50) DEFAULT 'en_cours'";
                        break;
                    case 'type':
                        $columnDefs[] = "type VARCHAR(50)";
                        break;
                    case 'nom':
                    case 'prenom':
                    case 'matricule':
                    case 'role':
                    case 'pool':
                        $columnDefs[] = "$column VARCHAR(100)";
                        break;
                    case 'pm_id':
                        $columnDefs[] = "pm_id INT";
                        break;
                    default:
                        $columnDefs[] = "$column VARCHAR(255)";
                }
            }
            
            $sql .= implode(", ", $columnDefs) . ")";
            $pdo->exec($sql);
            echo "Table $table créée\n";
        } else {
            echo "Table $table existe\n";
            
            // Vérifier les colonnes
            $stmt = $pdo->query("SHOW COLUMNS FROM $table");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($columns as $column) {
                if (!in_array($column, $existingColumns)) {
                    echo "Colonne manquante dans $table: $column\n";
                    
                    // Ajouter la colonne manquante
                    $sql = "ALTER TABLE $table ADD COLUMN ";
                    switch ($column) {
                        case 'utilisateur_id':
                            $sql .= "utilisateur_id INT";
                            break;
                        case 'date_entretien':
                            $sql .= "date_entretien DATETIME";
                            break;
                        case 'jours_restants':
                            $sql .= "jours_restants INT DEFAULT 0";
                            break;
                        case 'statut':
                            $sql .= "statut VARCHAR(50) DEFAULT 'en_cours'";
                            break;
                        case 'type':
                            $sql .= "type VARCHAR(50)";
                            break;
                        case 'nom':
                        case 'prenom':
                        case 'matricule':
                        case 'role':
                        case 'pool':
                            $sql .= "$column VARCHAR(100)";
                            break;
                        case 'pm_id':
                            $sql .= "pm_id INT";
                            break;
                        default:
                            $sql .= "$column VARCHAR(255)";
                    }
                    $pdo->exec($sql);
                    echo "Colonne $column ajoutée à $table\n";
                }
            }
        }
    }

    echo "Vérification terminée\n";

} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
