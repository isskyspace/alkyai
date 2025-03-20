<?php
// Informations de connexion à la base de données
$host = "localhost"; // Ou l'adresse de votre serveur PostgreSQL
$dbname = "AlkyAI";
$username = "postgres";
$password = "Kylian25#";
$port = "5432";

// Chaîne de connexion
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";

try {
    // Connexion à la base de données
    $conn = new PDO($dsn);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    die();
}

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $adresse = $_POST['adresse'];
    $apport = $_POST['apport'];

    // Préparation de la requête SQL pour insérer les données dans la table
    $query = "INSERT INTO adhésion (nom, prenom, email, tel, adresse, apport) VALUES (:nom, :prenom, :email, :tel, :adresse, :apport)";
    $stmt = $conn->prepare($query);

    // Liaison des paramètres pour éviter les injections SQL
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tel', $tel);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':apport', $apport);

    // Exécution de la requête
    if ($stmt->execute()) {
        echo "Données enregistrées avec succès !";
    } else {
        echo "Erreur : Les données n'ont pas pu être enregistrées.";
    }
}
?>
