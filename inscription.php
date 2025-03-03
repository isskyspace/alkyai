<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alkystore"; // Remplace par le nom de ta base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Vérifie si les données sont envoyées via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $date_naissance = $_POST['date_naissance'];

    // Préparer la requête SQL pour insérer les données
    $sql = "INSERT INTO users1 (prenom, nom, email, mot_de_passe, date_naissance) 
            VALUES ('$prenom', '$nom', '$email', '$mot_de_passe', '$date_naissance')";

    // Exécuter la requête
    if ($conn->query($sql) === TRUE) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur: " . $sql . "<br>" . $conn->error;
    }

    // Fermer la connexion
    $conn->close();
}
?>
