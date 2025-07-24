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

// Traitement du formulaire
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $poste = $_POST['poste'];
    $salaire = $_POST['salaire'];
    $date_embauche = $_POST['date_embauche'];
    
    // Gestion de l'upload de la photo
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
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
        $stmt = $pdo->prepare("INSERT INTO employes (nom, prenom, email, poste, salaire, date_embauche, photo) 
                              VALUES (:nom, :prenom, :email, :poste, :salaire, :date_embauche, :photo)");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':poste' => $poste,
            ':salaire' => $salaire,
            ':date_embauche' => $date_embauche,
            ':photo' => $photo
        ]);
        $message = '<div class="success-message">Employé enregistré avec succès!</div>';
    } catch (PDOException $e) {
        $message = '<div class="error-message">Erreur lors de l\'enregistrement: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrement Employé</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../css/inscription.css">
</head>
<body>
    <div class="container animate__animated animate__fadeInUp">
        <div class="form-header">
            <h1>Enregistrement d'un Employé</h1>
            <p>Remplissez le formulaire pour ajouter un nouvel employé</p>
        </div>
        
        <div class="form-body">
            <?php echo $message; ?>
            
            <form action="inscription.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="poste">Poste</label>
                    <input type="text" id="poste" name="poste" required>
                </div>
                
                <div class="form-group">
                    <label for="salaire">Salaire</label>
                    <input type="number" id="salaire" name="salaire" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="date_embauche">Date d'embauche</label>
                    <input type="date" id="date_embauche" name="date_embauche" required>
                </div>
                
                <div class="form-group">
                    <label for="photo">Photo de profil</label>
                    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
                    <img id="photoPreview" class="photo-preview">
                </div>
                
                <button type="submit" class="btn">Enregistrer</button>
                <a href="liste.php" class="btn" style="background: var(--dark-color); margin-left: 10px;">Voir la liste</a>
            </form>
        </div>
    </div>

    <script src="../script/inscription.js"></script>
</body>
</html>