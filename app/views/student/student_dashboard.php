<?php
require 'functions/Database.php';
require 'functions/User.php';
require 'functions/Course.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->connect();

$course = new Course($db);
$users = new User($db);

$user = $_SESSION['user'];
$lastCourse = $course->getDernierCoursConsulté($user['id']);

include 'templates/header.php';
include 'templates/navbar.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Bienvenue, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p></p>
        </div>
    </div>
    <div class="row">
        <!-- Derniers cours consultés -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Derniers cours consultés
                </div>
                <div class="card-body">
                    <?php if ($lastCourse): ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($lastCourse['name']); ?></h5>
                        <p class="card-text">
                            Thème: <?php echo htmlspecialchars($lastCourse['theme']); ?><br>
                            Domaine: <?php echo htmlspecialchars($lastCourse['domain']); ?>
                        </p>
                        <a href="#" class="btn btn-primary">Voir le cours</a>
                    <?php else: ?>
                        <h5 class="card-title">Aucuns cours n'a été consulté pour l'instant</h5>
                        <a href="#" class="btn btn-primary">Voir les cours</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quiz -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Quiz
                </div>
                <div class="card-body">
                    <h5 class="card-title">Participer aux Quiz</h5>
                    <p class="card-text">Testez vos connaissances avec nos quiz.</p>
                    <a href="#" class="btn btn-primary">Accéder aux Quiz</a>
                </div>
            </div>
        </div>

        <!-- Forum -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Forum
                </div>
                <div class="card-body">
                    <h5 class="card-title">Discussions du forum</h5>
                    <p class="card-text">Rejoignez les discussions sur le forum.</p>
                    <a href="forum/index.php" class="btn btn-primary">Accéder au Forum</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
