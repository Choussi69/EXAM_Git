<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'gestion_employes';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // Suppression de la photo si elle existe
        $stmt = $pdo->prepare("SELECT photo FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        $photo = $stmt->fetchColumn();
        
        if ($photo && file_exists($photo)) {
            unlink($photo);
        }
        
        // Suppression de l'employé
        $stmt = $pdo->prepare("DELETE FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: liste.php?message=delete_success");
        exit();
    } catch (PDOException $e) {
        header("Location: liste.php?message=delete_error");
        exit();
    }
}

// Recherche d'employés
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR poste LIKE ?";
    $searchTerm = "%$search%";
    $params = array_fill(0, 4, $searchTerm);
}

// Récupération des employés
$stmt = $pdo->prepare("SELECT * FROM employes $where ORDER BY nom ASC");
$stmt->execute($params);
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Employés</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/liste.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Liste des Employés</h1>
            <div>
                <a href="inscription.php" class="btn btn-add">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
            </div>
        </div>
        
        <?php
        if (isset($_GET['message'])) {
            if ($_GET['message'] === 'delete_success') {
                echo '<div class="message success-message animate__animated animate__fadeIn">Employé supprimé avec succès!</div>';
            } elseif ($_GET['message'] === 'delete_error') {
                echo '<div class="message error-message animate__animated animate__fadeIn">Erreur lors de la suppression de l\'employé</div>';
            }
        }
        ?>
        
        <div class="search-container animate__animated animate__fadeIn">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un employé..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        
        <div class="table-responsive">
            <table class="employees-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Poste</th>
                        <th>Salaire</th>
                        <th>Date Embauche</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employes)): ?>
                        <tr>
                            <td colspan="8" class="no-employees">Aucun employé trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($employes as $employe): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($employe['photo'])): ?>
                                        <img src="<?php echo $employe['photo']; ?>" alt="Photo" class="employee-photo">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #e9ecef; 
                                                    display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: #6c757d;"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($employe['nom']); ?></td>
                                <td><?php echo htmlspecialchars($employe['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($employe['email']); ?></td>
                                <td><?php echo htmlspecialchars($employe['poste']); ?></td>
                                <td><?php echo number_format($employe['salaire'], 2, ',', ' '); ?> €</td>
                                <td><?php echo date('d/m/Y', strtotime($employe['date_embauche'])); ?></td>
                                <td>
                                    <a href="modifier.php?id=<?php echo $employe['id']; ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <a href="liste.php?action=supprimer&id=<?php echo $employe['id']; ?>" 
                                       class="action-btn delete-btn" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../script/liste.js"></script>
</body>
</html>