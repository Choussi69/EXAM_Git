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

// Récupération de l'employé à modifier
$employe = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur lors de la récupération de l'employé: " . $e->getMessage());
    }
}

if (!$employe) {
    header("Location: liste.php");
    exit();
}

// Traitement du formulaire de modification
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $poste = $_POST['poste'];
    $salaire = $_POST['salaire'];
    $date_embauche = $_POST['date_embauche'];
    
    // Gestion de l'upload de la nouvelle photo
    $photo = $employe['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Supprimer l'ancienne photo si elle existe
        if ($photo && file_exists($photo)) {
            unlink($photo);
        }
        
        // Uploader la nouvelle photo
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $photoName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $photoPath = $uploadDir . $photoName;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
            $photo = $photoPath;
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE employes SET 
                              nom = :nom, 
                              prenom = :prenom, 
                              email = :email, 
                              poste = :poste, 
                              salaire = :salaire, 
                              date_embauche = :date_embauche, 
                              photo = :photo 
                              WHERE id = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':poste' => $poste,
            ':salaire' => $salaire,
            ':date_embauche' => $date_embauche,
            ':photo' => $photo,
            ':id' => $employe['id']
        ]);
        $message = '<div class="success-message">Employé modifié avec succès!</div>';
        
        // Mettre à jour les données de l'employé affiché
        $employe = array_merge($employe, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'poste' => $poste,
            'salaire' => $salaire,
            'date_embauche' => $date_embauche,
            'photo' => $photo
        ]);
    } catch (PDOException $e) {
        $message = '<div class="error-message">Erreur lors de la modification: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Employé</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/modifier.css">
</head>
<body>
    <div class="container">
        <div class="form-header">
            <a href="liste.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Modifier Employé</h1>
            <p>Modifiez les informations de l'employé</p>
        </div>
        
        <div class="form-body">
            <?php echo $message; ?>
            
            <form action="modifier.php?id=<?php echo $employe['id']; ?>" method="POST" enctype="multipart/form-data">
                <div class="photo-container">
                    <?php if (!empty($employe['photo'])): ?>
                        <img src="<?php echo $employe['photo']; ?>" alt="Photo actuelle" class="current-photo">
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; background-color: #e9ecef; 
                                    display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="color: #6c757d; font-size: 30px;"></i>
                        </div>
                    <?php endif; ?>
                    <img id="photoPreview" class="photo-preview">
                </div>
                
                <div class="form-group">
                    <label for="photo">Changer la photo</label>
                    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
                </div>
                
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($employe['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($employe['prenom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employe['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="poste">Poste</label>
                    <input type="text" id="poste" name="poste" value="<?php echo htmlspecialchars($employe['poste']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="salaire">Salaire</label>
                    <input type="number" id="salaire" name="salaire" step="0.01" 
                           value="<?php echo htmlspecialchars($employe['salaire']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_embauche">Date d'embauche</label>
                    <input type="date" id="date_embauche" name="date_embauche" 
                           value="<?php echo htmlspecialchars($employe['date_embauche']); ?>" required>
                </div>
                
                <button type="submit" class="btn">Enregistrer les modifications</button>
                <a href="liste.php" class="btn" style="background: var(--dark-color); margin-left: 10px;">Annuler</a>
            </form>
        </div>
    </div>

                        <script src="../script/modifier.js"></script>
</body>
</html>