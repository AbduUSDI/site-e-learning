<?php
session_start();
require_once '../functions/Database.php';
require_once '../functions/User.php';
require_once '../functions/Profile.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$database = new Database();
$db = $database->connect();

$user = new User2($db);
$profile = new Profile($db);

$userId = $_SESSION['user']['id'];
$currentUser = $user->getUserById($userId);
$userProfile = $profile->getProfileByUserId($userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
    $biographie = filter_input(INPUT_POST, 'biographie', FILTER_SANITIZE_STRING);

    // Handle profile picture upload
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileInfo = pathinfo($_FILES['photo_profil']['name']);
        $fileExt = strtolower($fileInfo['extension']);

        if (in_array($fileExt, $allowed)) {
            $fileName = $userId . '.' . $fileExt;
            $filePath = '../uploads/profile_pictures/' . $fileName;

            if (move_uploaded_file($_FILES['photo_profil']['tmp_name'], $filePath)) {
                $profile->updateProfilePicture($userId, $fileName);
            }
        }
    }

    $result = $profile->updateProfile($userId, $prenom, $nom, $date_naissance, $biographie);

    $message = $result ? "Profil mis à jour avec succès." : "Erreur lors de la mise à jour du profil.";
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $result ? 'success' : 'danger';

    header('Location: my_profile.php');
    exit;
}

include '../templates/header.php';
include 'templates/navbar_forum.php';
?>

<div class="container mt-5">
    <h1>Modifier le profil</h1>
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userProfile['prenom'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($userProfile['nom'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($userProfile['date_naissance'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="biographie">Biographie</label>
            <textarea class="form-control" id="biographie" name="biographie" rows="5"><?php echo htmlspecialchars($userProfile['biographie'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="photo_profil">Photo de profil</label>
            <input type="file" class="form-control-file" id="photo_profil" name="photo_profil">
        </div>
        <button type="submit" class="btn btn-success mt-3">Enregistrer les modifications</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
