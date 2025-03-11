<?php
// Paramètres de connexion à la base de données (à remplacer par les infos de Render)
$servername = "votre_db_url_render";  // L'URL de votre base de données (fournie par Render)
$username = "votre_utilisateur";     // Utilisateur MySQL fourni par Render
$password = "votre_mot_de_passe";    // Mot de passe MySQL fourni par Render
$dbname = "alky_ai";                 // Nom de la base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
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

    // Préparation et exécution de la requête SQL pour insérer les données
    $stmt = $conn->prepare("INSERT INTO adhesion (nom, prenom, email, tel, adresse, apport) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nom, $prenom, $email, $tel, $adresse, $apport);

    // Exécution de la requête
    if ($stmt->execute()) {
        echo "Données enregistrées avec succès!";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    // Fermeture de la requête et de la connexion
    $stmt->close();
    $conn->close();
}
?>
