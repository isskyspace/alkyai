<?php
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_log("Test d'erreur PHP");

// Afficher toutes les erreurs PHP pour débogage
error_reporting(E_ALL);  // Affiche toutes les erreurs PHP
ini_set('display_errors', 1);  // Affiche les erreurs directement dans le navigateur

// Loguer toute erreur avant d'exécuter la connexion à la base

error_reporting(E_ALL);  // Affiche toutes les erreurs PHP
ini_set('display_errors', 1);  // Active l'affichage des erreurs

header("Content-Type: application/json");

// Permettre l'accès cross-origin (CORS)
header("Access-Control-Allow-Origin: https://alkyai.fr");  // Remplacez par l'URL de votre frontend
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gérer les requêtes OPTIONS avant le POST (pour vérifier les en-têtes CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
