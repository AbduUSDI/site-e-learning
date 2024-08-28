<?php

/**
 * Classe pour établir une connexion à la base de données
 */
class Database {
    private $hote = 'cky0ko.stackhero-network.com';
    private $nom_base_de_donnees = 'e_learning';
    private $identifiant = 'root';
    private $mot_de_passe = 'a1QDfFRVtBpNgZvJB8cy0dtUlllKXyOL';
    private $port = '5040';
    private $connexion;

    public function getConnection() {
        $this->connexion = null;

        try {
            // Ajouter les options SSL
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => "C:\\Users\\Abdu's PC\\Downloads\\isrgrootx1 (1).pem",
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
            ];

            $this->connexion = new PDO(
                "mysql:host=" . $this->hote . ";port=" . $this->port . ";dbname=" . $this->nom_base_de_donnees,
                $this->identifiant,
                $this->mot_de_passe,
                $options // Ajout des options SSL
            );

            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $erreur) {
            // Logger l'erreur et lancer une exception personnalisée
            error_log("Erreur de connexion à la base de données : " . $erreur->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }

        return $this->connexion;
    }
}