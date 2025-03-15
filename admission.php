<?php
// Informations de connexion à la base de données PostgreSQL
$host = "dpg-cv8aij8gph6c73brekk0-a"; // Remplacez par l'hôte de votre base de données PostgreSQL sur Render
$dbname = "AlkyAI"; // Nom de votre base de données
$username = "root"; // Nom d'utilisateur PostgreSQL
$password = "EbbN7NlSbZnnfYk9whvIn8smLIXCQoj8"; // Mot de passe PostgreSQL
$port = "5432"; // Port par défaut pour PostgreSQL

// Création de la connexion
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";

try {
    $conn = new PDO($dsn);
    // Définit le mode d'erreur PDO pour les exceptions
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