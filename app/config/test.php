<?php
try {
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => "C:\\Users\\Abdu's PC\\Downloads\\isrgrootx1 (1).pem",
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $connexion = new PDO(
        "mysql:host=cky0ko.stackhero-network.com;port=5040;dbname=e_learning",
        "Abdurahman",
        "Abdufufu2525+",
        $options
    );

    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie!";
} catch(PDOException $erreur) {
    echo "Erreur de connexion : " . $erreur->getMessage();
}
