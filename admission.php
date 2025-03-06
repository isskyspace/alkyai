<?php
error_reporting(E_ALL);  // Affiche toutes les erreurs PHP
ini_set('display_errors', 1);  // Active l'affichage des erreurs
ini_set('log_errors', 1);  // Active la journalisation des erreurs
ini_set('error_log', 'php_errors.log');  // Journaliser dans un fichier log

header("Content-Type: application/json");

// Vérification des données POST
if (!isset($_POST["prenom"], $_POST["nom"], $_POST["email"], $_POST["codemail"], $_POST["date_naissance"], $_POST["profession"])) {
    echo json_encode(["success" => false, "message" => "Données incomplètes."]);
    exit;
}

// Connexion à la base de données via l'URL MySQL depuis la variable d'environnement
$mysql_url = getenv('MYSQL_URL'); // Utiliser la variable d'environnement MYSQL_URL

// Extraire les informations de l'URL MySQL
$parsed_url = parse_url($mysql_url);
$host = $parsed_url['host'];
$user = $parsed_url['user'];
$password = $parsed_url['pass'];
$dbname = ltrim($parsed_url['path'], '/');
$port = $parsed_url['port'];

// Créer une connexion à la base de données
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Vérifier la connexion
if ($conn->connect_error) {
    error_log("Erreur de connexion à la base de données : " . $conn->connect_error); // Log de l'erreur
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
$sql = "INSERT INTO users1 (prenom, nom, email, codemail, date_naissance, profession) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    error_log("Erreur préparation de la requête : " . $conn->error);  // Log de l'erreur de préparation
    echo json_encode(["success" => false, "message" => "Erreur lors de la préparation de la requête : " . $conn->error]);
    exit;
}

$stmt->bind_param("ssssss", $prenom, $nom, $email, $codemail, $date_naissance, $profession);

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Inscription réussie !"]);
} else {
    error_log("Erreur d'exécution de la requête : " . $stmt->error);  // Log de l'erreur d'exécution
    echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription : " . $stmt->error]);
}

// CORS (pour accepter les requêtes depuis le frontend)
header("Access-Control-Allow-Origin: https://alkyai.fr");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
