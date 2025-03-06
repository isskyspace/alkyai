<?php
error_reporting(E_ALL);  // Affiche toutes les erreurs PHP
ini_set('display_errors', 1);  // Active l'affichage des erreurs

header("Content-Type: application/json");

// Vérification des données POST
if (!isset($_POST["prenom"], $_POST["nom"], $_POST["email"], $_POST["codemail"], $_POST["date_naissance"], $_POST["profession"])) {
    echo json_encode(["success" => false, "message" => "Données incomplètes."]);
    exit;
}

// Connexion à la base de données avec la variable d'environnement Railway
$database_url = getenv('MYSQL_URL'); // Récupérer l'URL de la base de données depuis l'environnement

// Analyser l'URL de la base de données
$parsed_url = parse_url($database_url);

$host = $parsed_url['host'];      // L'hôte (ex: mysql-4ikf.railway.internal)
$user = $parsed_url['user'];      // Utilisateur (ex: root)
$password = $parsed_url['pass'];  // Mot de passe (ex: rNIFDulTqfkvJYuAurNQeVZqtNKUpAwq)
$dbname = ltrim($parsed_url['path'], '/'); // Nom de la base de données (ex: railway)

// Connexion à la base de données
$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Erreur de connexion : " . $conn->connect_error]);
    exit;
}

// Sécuriser les données
$prenom = htmlspecialchars($_POST["prenom"]);
$nom = htmlspecialchars($_POST["nom"]);
$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
$codemail = password_hash($_POST["codemail"], PASSWORD_DEFAULT); // Sécuriser le mot de passe
$date_naissance = htmlspecialchars($_POST["date_naissance"]);
$profession = htmlspecialchars($_POST["profession"]);

// Vérifier l'email valide
if (!$email) {
    echo json_encode(["success" => false, "message" => "Email invalide."]);
    exit;
}

// Insérer dans la base de données
$sql = "INSERT INTO users (prenom, nom, email, codemail, date_naissance, profession) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $prenom, $nom, $email, $codemail, $date_naissance, $profession);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Inscription réussie !"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription : " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
