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

// Traitement de la recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$employes = [];

if (!empty($search)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM employes 
                              WHERE nom LIKE :search 
                              OR prenom LIKE :search 
                              OR email LIKE :search 
                              OR poste LIKE :search 
                              ORDER BY nom ASC");
        $stmt->execute([':search' => "%$search%"]);
        $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erreur de recherche : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'Employés</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            animation: fadeInDown 0.8s;
        }
        
        .search-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .search-header h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .search-header p {
            color: var(--dark-color);
            opacity: 0.8;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 30px;
        }
        
        .search-input {
            width: 100%;
            padding: 18px 25px;
            padding-left: 60px;
            border: none;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            transition: all 0.4s;
            background-color: white;
        }
        
        .search-input:focus {
            outline: none;
            box-shadow: 0 10px 40px rgba(67, 97, 238, 0.2);
            transform: translateY(-2px);
        }
        
        .search-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 22px;
        }
        
        .results-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.8s;
        }
        
        .results-count {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .employee-card {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s;
        }
        
        .employee-card:last-child {
            border-bottom: none;
        }
        
        .employee-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .employee-photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-color);
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .employee-info {
            flex-grow: 1;
        }
        
        .employee-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .employee-position {
            color: var(--primary-color);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .employee-email {
            color: #6c757d;
            font-size: 14px;
        }
        
        .employee-salary {
            font-weight: 600;
            color: var(--success-color);
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: var(--dark-color);
        }
        
        .no-results i {
            font-size: 50px;
            color: var(--accent-color);
            margin-bottom: 20px;
            display: block;
        }
        
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }
        
        .back-btn i {
            margin-right: 8px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .employee-card {
            animation: fadeIn 0.5s forwards;
            opacity: 0;
        }
        
        .employee-card:nth-child(1) { animation-delay: 0.1s; }
        .employee-card:nth-child(2) { animation-delay: 0.2s; }
        .employee-card:nth-child(3) { animation-delay: 0.3s; }
        .employee-card:nth-child(4) { animation-delay: 0.4s; }
        .employee-card:nth-child(5) { animation-delay: 0.5s; }
        .employee-card:nth-child(6) { animation-delay: 0.6s; }
        .employee-card:nth-child(7) { animation-delay: 0.7s; }
        .employee-card:nth-child(8) { animation-delay: 0.8s; }
        .employee-card:nth-child(9) { animation-delay: 0.9s; }
        .employee-card:nth-child(10) { animation-delay: 1s; }
        
        /* Effet de surbrillance pour les résultats */
        .highlight {
            background-color: #fffde7;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <div class="search-header">
            <h1>Recherche d'Employés</h1>
            <p>Trouvez rapidement les employés que vous cherchez</p>
        </div>
        
        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Entrez un nom, prénom, poste ou email..." 
                   value="<?php echo htmlspecialchars($search); ?>" autocomplete="off" autofocus>
        </div>
        
        <div class="results-container" id="resultsContainer">
            <?php if (!empty($search)): ?>
                <div class="results-count">
                    <?php echo count($employes); ?> résultat(s) pour "<?php echo htmlspecialchars($search); ?>"
                </div>
                
                <?php if (empty($employes)): ?>
                    <div class="no-results animate__animated animate__fadeIn">
                        <i class="fas fa-user-times"></i>
                        <h3>Aucun employé trouvé</h3>
                        <p>Essayez avec d'autres termes de recherche</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($employes as $employe): ?>
                        <a href="modifier.php?id=<?php echo $employe['id']; ?>" class="employee-card">
                            <?php if (!empty($employe['photo'])): ?>
                                <img src="<?php echo $employe['photo']; ?>" alt="Photo" class="employee-photo">
                            <?php else: ?>
                                <div class="employee-photo" style="background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="color: #6c757d; font-size: 30px;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="employee-info">
                                <h3 class="employee-name">
                                    <?php 
                                        echo preg_replace("/($search)/i", '<span class="highlight">$1</span>', htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']));
                                    ?>
                                </h3>
                                <p class="employee-position">
                                    <?php 
                                        echo preg_replace("/($search)/i", '<span class="highlight">$1</span>', htmlspecialchars($employe['poste']));
                                    ?>
                                </p>
                                <p class="employee-email">
                                    <?php 
                                        echo preg_replace("/($search)/i", '<span class="highlight">$1</span>', htmlspecialchars($employe['email']));
                                    ?>
                                </p>
                            </div>
                            
                            <div class="employee-salary">
                                <?php echo number_format($employe['salaire'], 2, ',', ' '); ?> €
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results animate__animated animate__fadeIn">
                    <i class="fas fa-search"></i>
                    <h3>Commencez votre recherche</h3>
                    <p>Entrez un nom, un prénom, un poste ou un email dans le champ ci-dessus</p>
                </div>
            <?php endif; ?>
        </div>
        
        <center>
            <a href="liste.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Retour à la liste complète
            </a>
        </center>
    </div>

    <script>
        // Recherche dynamique avec AJAX
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchValue = this.value.trim();
            const resultsContainer = document.getElementById('resultsContainer');
            
            if (searchValue.length > 0) {
                // Animation de chargement
                resultsContainer.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-spinner fa-spin"></i>
                        <h3>Recherche en cours...</h3>
                    </div>
                `;
                
                // Requête AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('GET', `search.php?search=${encodeURIComponent(searchValue)}`, true);
                
                xhr.onload = function() {
                    if (this.status === 200) {
                        // Extraire seulement la partie results-container du HTML retourné
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(this.responseText, 'text/html');
                        const newResults = doc.getElementById('resultsContainer').innerHTML;
                        
                        // Mettre à jour les résultats
                        resultsContainer.innerHTML = newResults;
                        
                        // Animation des nouveaux résultats
                        const cards = document.querySelectorAll('.employee-card');
                        cards.forEach((card, index) => {
                            card.style.animationDelay = `${index * 0.1}s`;
                        });
                    }
                };
                
                xhr.send();
            } else {
                // Afficher l'état initial si la recherche est vide
                resultsContainer.innerHTML = `
                    <div class="no-results animate__animated animate__fadeIn">
                        <i class="fas fa-search"></i>
                        <h3>Commencez votre recherche</h3>
                        <p>Entrez un nom, un prénom, un poste ou un email dans le champ ci-dessus</p>
                    </div>
                `;
            }
        });
        
        // Focus sur le champ de recherche au chargement de la page
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('searchInput').focus();
        });
    </script>
</body>
</html>