-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 26 août 2024 à 11:08
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `e_learning`
--
CREATE DATABASE IF NOT EXISTS `e_learning` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `e_learning`;
-- --------------------------------------------------------

--
-- Structure de la table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `formation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `title`, `created_at`, `formation_id`) VALUES
(39, 'Introduction', '2024-08-25 22:58:19', 18);

-- --------------------------------------------------------

--
-- Structure de la table `category_progress`
--

CREATE TABLE `category_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL DEFAULT 'not_started'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `category_progress`
--

INSERT INTO `category_progress` (`id`, `user_id`, `category_id`, `status`) VALUES
(12, 4, 39, 'in_progress');

-- --------------------------------------------------------

--
-- Structure de la table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `certificate_number` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exam_feedback`
--

CREATE TABLE `exam_feedback` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `exam_submissions`
--

CREATE TABLE `exam_submissions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formations`
--

CREATE TABLE `formations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formations`
--

INSERT INTO `formations` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(18, 'HTML et CSS', 'Cette formation sur HTML et CSS est con&ccedil;ue pour les d&eacute;butants qui souhaitent apprendre &agrave; cr&eacute;er des sites web modernes et r&eacute;actifs. Elle couvre les concepts fondamentaux du HTML (HyperText Markup Language) et du CSS (Cascading Style Sheets), les deux technologies de base pour la cr&eacute;ation de pages web. Au cours de cette formation, vous apprendrez &agrave; structurer le contenu de vos pages web avec HTML et &agrave; les styliser avec CSS pour leur donner un aspect professionnel et attractif. Les vid&eacute;os vous guideront &eacute;tape par &eacute;tape, avec des exemples pratiques et des exercices pour renforcer votre compr&eacute;hension. &Agrave; la fin de la formation, vous serez capable de concevoir et de d&eacute;velopper vos propres sites web, de la cr&eacute;ation de la structure de base &agrave; l&#039;application de styles avanc&eacute;s.', '2024-08-25 20:58:03', '2024-08-25 20:58:03');

-- --------------------------------------------------------

--
-- Structure de la table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `post_type` enum('thread','reply') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `parent_id`, `user_id`, `title`, `body`, `post_type`, `created_at`, `updated_at`) VALUES
(10, NULL, 4, 'Discussion test', 'Contenu test.', 'thread', '2024-08-23 19:36:51', '2024-08-23 19:36:51'),
(11, 10, 4, NULL, 'Test\r\n', 'reply', '2024-08-24 05:09:08', '2024-08-24 05:09:08'),
(12, 10, 4, NULL, 'test', 'reply', '2024-08-24 05:09:21', '2024-08-24 05:09:21'),
(13, 10, 4, NULL, 'test', 'reply', '2024-08-24 05:09:24', '2024-08-24 05:09:24'),
(14, NULL, 5, 'test', 'test', 'thread', '2024-08-25 15:22:11', '2024-08-25 15:22:11');

-- --------------------------------------------------------

--
-- Structure de la table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `since` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `since`) VALUES
(4, 5, 4, '2024-08-25 17:53:30');

-- --------------------------------------------------------

--
-- Structure de la table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `sender_id`, `receiver_id`, `status`, `sent_at`) VALUES
(3, 2, 4, 'accepted', '2024-08-25 05:14:23'),
(4, 4, 2, 'accepted', '2024-08-25 12:17:04'),
(5, 4, 2, 'accepted', '2024-08-25 12:35:00'),
(6, 5, 4, 'accepted', '2024-08-25 15:22:37');

-- --------------------------------------------------------

--
-- Structure de la table `lives`
--

CREATE TABLE `lives` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date` datetime NOT NULL,
  `link` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lives`
--

INSERT INTO `lives` (`id`, `utilisateur_id`, `title`, `description`, `date`, `link`, `created_at`) VALUES
(1, 5, 'zadef', 'azdazf', '2024-08-25 18:50:00', 'https://votresite.com/live-session/123', '2024-08-25 16:50:54'),
(2, 6, 'Live 2', 'Live 2 description', '2024-08-26 10:20:00', 'https://votresite.com/live-session/123', '2024-08-25 17:22:35'),
(3, 5, 'Live test id', 'test', '2024-08-31 22:45:00', 'https://votresite.com/live-session/123', '2024-08-25 17:42:11');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `body`, `created_at`) VALUES
(14, 2, 4, 'ezegaef', '2024-08-25 05:31:15'),
(15, 5, 4, 'test messsage', '2024-08-25 15:22:45'),
(16, 2, 6, 'test message', '2024-08-25 22:17:44');

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `view_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pages`
--

INSERT INTO `pages` (`id`, `subcategory_id`, `title`, `content`, `video_url`, `created_at`, `view_count`) VALUES
(34, 30, 'Introduction', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: C\'est quoi HTML et CSS ? Et pourquoi l\'apprendre ?</h2>\n\n<h3 style=\"color: #FF5722;\">Introduction à HTML et CSS</h3>\n\n<p><strong style=\"color: #009688;\">HTML (HyperText Markup Language)</strong> et <strong style=\"color: #009688;\">CSS (Cascading Style Sheets)</strong> sont les deux technologies essentielles pour créer et styliser des pages web. Elles travaillent main dans la main pour construire l\'apparence et la structure d\'un site web.</p>\n\n<p><strong style=\"color: #009688;\">HTML</strong> est le langage de balisage utilisé pour structurer le contenu des pages web. Il permet de définir des éléments comme les titres, paragraphes, images, liens, et bien plus encore. Chaque élément HTML est encadré par des balises qui dictent au navigateur comment afficher le contenu.</p>\n\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\n    <p><strong>Exemple de balise HTML :</strong></p>\n    <pre><code><h1>Bienvenue sur mon site web</h1>\n<p>Ceci est un paragraphe de texte.</p></code></pre>\n</div>\n\n<p><strong style=\"color: #009688;\">CSS</strong>, quant à lui, est utilisé pour styliser les éléments définis par HTML. Il permet d’appliquer des styles visuels tels que les couleurs, polices, espacements, et bordures, transformant ainsi une structure HTML basique en un design attractif et moderne.</p>\n\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\n    <p><strong>Exemple de code CSS :</strong></p>\n    <pre><code>h1 {\n    color: blue;\n    font-family: Arial, sans-serif;\n}\n\np {\n    font-size: 16px;\n    line-height: 1.5;\n}</code></pre>\n</div>\n\n<h3 style=\"color: #FF5722;\">Pourquoi Apprendre HTML et CSS ?</h3>\n\n<p>Apprendre HTML et CSS est une étape cruciale pour toute personne souhaitant créer des sites web ou entrer dans le domaine du développement web. Voici quelques raisons pour lesquelles ces compétences sont indispensables :</p>\n\n<ul style=\"list-style-type: square; margin-left: 20px;\">\n    <li><strong style=\"color: #4CAF50;\">Création de Sites Web</strong> : HTML et CSS sont la base de tout site web. Ils vous permettent de créer des pages web personnalisées à partir de zéro.</li>\n    <li><strong style=\"color: #4CAF50;\">Personnalisation</strong> : Avec ces compétences, vous pouvez créer des designs uniques qui répondent aux besoins spécifiques de vos projets ou de vos clients.</li>\n    <li><strong style=\"color: #4CAF50;\">Fondamentaux du Web</strong> : La maîtrise de HTML et CSS est essentielle même si vous utilisez des frameworks ou des CMS plus avancés. Cela vous donnera une compréhension solide du fonctionnement des sites web.</li>\n    <li><strong style=\"color: #4CAF50;\">Opportunités de Carrière</strong> : Le développement web est un domaine en pleine expansion, et une bonne maîtrise de HTML et CSS ouvre de nombreuses opportunités professionnelles.</li>\n    <li><strong style=\"color: #4CAF50;\">Accessibilité et SEO</strong> : Comprendre HTML et CSS vous aide à créer des sites accessibles à tous et optimisés pour les moteurs de recherche, ce qui est crucial pour la visibilité en ligne.</li>\n</ul>\n\n<h3 style=\"color: #FF5722;\">Conclusion</h3>\n\n<p>En apprenant HTML et CSS, vous acquérez les compétences nécessaires pour transformer vos idées en sites web concrets et esthétiquement plaisants. Cette connaissance constitue la base de tout développement web, vous préparant à concevoir des sites web fonctionnels, accessibles et optimisés.</p>\n\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\n\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Ce cours constitue la première étape de votre formation. Si vous avez d\'autres thèmes à aborder, je suis prêt à rédiger le contenu pour les prochaines vidéos.</p>\n\n<div style=\"text-align: center; margin-top: 30px;\">\n    <p style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\n        Regarder la vidéo explicative\n    </p>\n</div>\n', '../../../../public/image_and_video/mp4/HTML_1.mp4', '2024-08-25 23:11:49', 8),
(37, 30, 'Votre premi&egrave;re page web en HTML', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: Créer Votre Première Page Web</h2>\r\n\r\n<h3 style=\"color: #FF5722;\">Introduction</h3>\r\n\r\n<p>Dans ce cours, vous allez apprendre à créer votre première page web en utilisant les éléments de base que nous avons vus précédemment. Nous allons assembler les balises HTML pour créer une structure simple mais fonctionnelle, puis y ajouter du contenu. À la fin de ce cours, vous aurez une page web de base que vous pourrez afficher dans n\'importe quel navigateur web.</p>\r\n\r\n<h3 style=\"color: #FF5722;\">Structure de Base d\'une Page HTML</h3>\r\n\r\n<p>Chaque page web commence par une structure HTML de base. Cette structure comprend les balises essentielles qui forment la fondation de votre document HTML :</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;!DOCTYPE html&gt;</strong> : Indique au navigateur que le document est un document HTML5.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;html&gt;</strong> : Enveloppe tout le contenu de la page.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;head&gt;</strong> : Contient des métadonnées sur la page, comme le titre et les liens vers des styles ou des scripts.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;title&gt;</strong> : Définit le titre de la page qui apparaît dans l\'onglet du navigateur.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;body&gt;</strong> : Contient tout le contenu visible de la page, comme les textes, images, et liens.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Structure de Base :</strong></p>\r\n    <pre><code>&lt;!DOCTYPE html&gt;\r\n&lt;html&gt;\r\n&lt;head&gt;\r\n    &lt;title&gt;Ma Première Page Web&lt;/title&gt;\r\n&lt;/head&gt;\r\n&lt;body&gt;\r\n    &lt;h1&gt;Bienvenue sur ma première page web!&lt;/h1&gt;\r\n    &lt;p&gt;Ceci est un paragraphe de texte simple.&lt;/p&gt;\r\n&lt;/body&gt;\r\n&lt;/html&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Ajouter du Contenu à la Page</h3>\r\n\r\n<p>Une fois la structure de base en place, vous pouvez commencer à ajouter du contenu à votre page. Le contenu peut inclure des titres, des paragraphes, des images, et des liens. Voici comment vous pouvez structurer une page simple avec ces éléments :</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Page Web :</strong></p>\r\n    <pre><code>&lt;!DOCTYPE html&gt;\r\n&lt;html&gt;\r\n&lt;head&gt;\r\n    &lt;title&gt;Ma Première Page Web&lt;/title&gt;\r\n&lt;/head&gt;\r\n&lt;body&gt;\r\n    &lt;h1&gt;Bienvenue sur ma première page web!&lt;/h1&gt;\r\n    \r\n    &lt;p&gt;Ceci est un paragraphe de texte simple. Vous pouvez ajouter plusieurs paragraphes pour structurer votre contenu.&lt;/p&gt;\r\n\r\n    &lt;h2&gt;À propos de moi&lt;/h2&gt;\r\n    &lt;p&gt;Je suis un développeur débutant apprenant à créer des pages web avec HTML et CSS.&lt;/p&gt;\r\n\r\n    &lt;h2&gt;Mes Centres d\'Intérêt&lt;/h2&gt;\r\n    &lt;ul&gt;\r\n        &lt;li&gt;Développement Web&lt;/li&gt;\r\n        &lt;li&gt;Design Graphique&lt;/li&gt;\r\n        &lt;li&gt;Photographie&lt;/li&gt;\r\n    &lt;/ul&gt;\r\n\r\n    &lt;p&gt;Visitez mon &lt;a href=\"https://www.example.com\"&gt;site web&lt;/a&gt; pour plus d\'informations.&lt;/p&gt;\r\n\r\n    &lt;img src=\"https://via.placeholder.com/150\" alt=\"Image d\'exemple\"&gt;\r\n\r\n&lt;/body&gt;\r\n&lt;/html&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Explication du Code</h3>\r\n\r\n<p>Dans l\'exemple ci-dessus :</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong>&lt;h1&gt;</strong> est utilisé pour le titre principal de la page.</li>\r\n    <li><strong>&lt;p&gt;</strong> est utilisé pour les paragraphes de texte.</li>\r\n    <li><strong>&lt;h2&gt;</strong> est utilisé pour les sous-titres, qui divisent le contenu en sections claires.</li>\r\n    <li><strong>&lt;ul&gt;</strong> et <strong>&lt;li&gt;</strong> sont utilisés pour créer une liste non ordonnée (avec des puces).</li>\r\n    <li><strong>&lt;a&gt;</strong> est utilisé pour créer un lien hypertexte.</li>\r\n    <li><strong>&lt;img&gt;</strong> est utilisé pour insérer une image dans la page.</li>\r\n</ul>\r\n\r\n<h3 style=\"color: #FF5722;\">Conclusion</h3>\r\n\r\n<p>Félicitations! Vous avez créé votre première page web. Ce n\'est que le début. En comprenant ces concepts de base, vous pouvez maintenant commencer à explorer plus de fonctionnalités HTML et à enrichir vos pages web. Dans les prochaines leçons, nous apprendrons comment styliser cette page en utilisant CSS pour lui donner un aspect professionnel et personnalisé.</p>\r\n\r\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\r\n\r\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Prêt à passer à l\'étape suivante? Continuons à apprendre en ajoutant des styles CSS à votre page web!</p>\r\n\r\n<div style=\"text-align: center; margin-top: 30px;\">\r\n    <a href=\"#\" style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\r\n        Démarrer le prochain cours\r\n    </a>\r\n</div>\r\n', '../../../../public/image_and_video/mp4/HTML_2.mp4', '2024-08-26 10:20:49', 0),
(38, 30, 'Fonctionnement des balises', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: Les Balises HTML et leurs Utilités</h2>\r\n\r\n<h3 style=\"color: #FF5722;\">Introduction aux Balises HTML</h3>\r\n\r\n<p>Les balises HTML sont les blocs de construction d\'une page web. Elles permettent de structurer et d\'organiser le contenu de la page en indiquant au navigateur comment chaque élément doit être affiché. Chaque balise HTML est entourée de chevrons &lt; &gt; et vient généralement en paire, une balise ouvrante et une balise fermante, comme dans cet exemple :</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <pre><code>&lt;p&gt;Ceci est un paragraphe.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<p>Voyons maintenant quelques balises HTML couramment utilisées et leur utilité.</p>\r\n\r\n<h3 style=\"color: #FF5722;\">Balises de Structure</h3>\r\n\r\n<p>Les balises de structure définissent la disposition de base du contenu sur une page web.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;html&gt;</strong> : Cette balise enveloppe tout le contenu de la page. Elle est la racine de tout document HTML.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;head&gt;</strong> : Contient des métadonnées sur la page, comme le titre (qui apparaît dans l\'onglet du navigateur) et des liens vers des feuilles de style ou des scripts.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;title&gt;</strong> : Définit le titre de la page, qui apparaît dans l\'onglet du navigateur.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;body&gt;</strong> : Enveloppe tout le contenu visible sur la page, comme les textes, images, vidéos, etc.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;html&gt;\r\n&lt;head&gt;\r\n    &lt;title&gt;Titre de la page&lt;/title&gt;\r\n&lt;/head&gt;\r\n&lt;body&gt;\r\n    &lt;h1&gt;Bienvenue sur mon site&lt;/h1&gt;\r\n    &lt;p&gt;Ceci est un exemple de paragraphe.&lt;/p&gt;\r\n&lt;/body&gt;\r\n&lt;/html&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balises de Texte</h3>\r\n\r\n<p>Les balises de texte sont utilisées pour formater et organiser le contenu textuel sur une page web.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;h1&gt; à &lt;h6&gt;</strong> : Représentent les titres et sous-titres, du plus important (&lt;h1&gt;) au moins important (&lt;h6&gt;).</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;p&gt;</strong> : Définit un paragraphe de texte.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;strong&gt;</strong> : Met en évidence un texte en gras, indiquant une importance particulière.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;em&gt;</strong> : Met en italique un texte pour en souligner l\'emphase.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;br&gt;</strong> : Insère un saut de ligne, permettant de passer à la ligne suivante.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;h1&gt;Mon Premier Titre&lt;/h1&gt;\r\n&lt;p&gt;Voici un paragraphe avec un &lt;strong&gt;texte en gras&lt;/strong&gt; et un &lt;em&gt;texte en italique&lt;/em&gt;.&lt;/p&gt;\r\n&lt;br&gt;\r\n&lt;h2&gt;Un Sous-Titre&lt;/h2&gt;\r\n&lt;p&gt;Un autre paragraphe pour montrer l\'usage des titres et sous-titres.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balises de Liens et d\'Images</h3>\r\n\r\n<p>Les balises de liens et d\'images sont cruciales pour rendre une page web interactive et visuellement attrayante.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;a&gt;</strong> : Crée un lien hypertexte qui permet de naviguer vers une autre page ou une autre partie de la même page. L\'attribut <code>href</code> spécifie la destination du lien.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;img&gt;</strong> : Intègre une image dans la page. L\'attribut <code>src</code> spécifie le chemin de l\'image, et <code>alt</code> fournit un texte alternatif si l\'image ne peut être affichée.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Visitez mon &lt;a href=\"https://www.example.com\"&gt;site web&lt;/a&gt; pour plus d\'informations.&lt;/p&gt;\r\n&lt;p&gt;&lt;img src=\"image.jpg\" alt=\"Description de l\'image\"&gt;&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balises de Listes</h3>\r\n\r\n<p>Les balises de listes permettent de créer des listes ordonnées ou non ordonnées pour organiser les informations.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;ul&gt;</strong> : Crée une liste non ordonnée, généralement avec des puces.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;ol&gt;</strong> : Crée une liste ordonnée, généralement numérotée.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;li&gt;</strong> : Représente un élément de liste, utilisé à l\'intérieur de &lt;ul&gt; ou &lt;ol&gt;.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;h3&gt;Liste non ordonnée&lt;/h3&gt;\r\n&lt;ul&gt;\r\n    &lt;li&gt;Élément 1&lt;/li&gt;\r\n    &lt;li&gt;Élément 2&lt;/li&gt;\r\n    &lt;li&gt;Élément 3&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;h3&gt;Liste ordonnée&lt;/h3&gt;\r\n&lt;ol&gt;\r\n    &lt;li&gt;Premier élément&lt;/li&gt;\r\n    &lt;li&gt;Deuxième élément&lt;/li&gt;\r\n    &lt;li&gt;Troisième élément&lt;/li&gt;\r\n&lt;/ol&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balises de Tableaux</h3>\r\n\r\n<p>Les tableaux sont utilisés pour afficher des données en lignes et en colonnes.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;table&gt;</strong> : Crée un tableau.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;tr&gt;</strong> : Définit une ligne de tableau.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;td&gt;</strong> : Définit une cellule dans une ligne de tableau.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;th&gt;</strong> : Définit une cellule d\'en-tête dans une ligne de tableau, généralement en gras et centré.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;table border=\"1\"&gt;\r\n    &lt;tr&gt;\r\n        &lt;th&gt;Nom&lt;/th&gt;\r\n        &lt;th&gt;Âge&lt;/th&gt;\r\n    &lt;/tr&gt;\r\n    &lt;tr&gt;\r\n        &lt;td&gt;Alice&lt;/td&gt;\r\n        &lt;td&gt;25&lt;/td&gt;\r\n    &lt;/tr&gt;\r\n    &lt;tr&gt;\r\n        &lt;td&gt;Bob&lt;/td&gt;\r\n        &lt;td&gt;30&lt;/td&gt;\r\n    &lt;/tr&gt;\r\n&lt;/table&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Conclusion</h3>\r\n\r\n<p>Les balises HTML sont essentielles pour structurer et styliser le contenu d\'une page web. Chaque balise a un rôle spécifique, et une bonne maîtrise de ces balises vous permettra de créer des pages web claires, fonctionnelles et esthétiquement plaisantes.</p>\r\n\r\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\r\n\r\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Ce cours couvre les balises de base en HTML. Si vous souhaitez approfondir vos connaissances, passez au prochain module où nous explorerons des concepts plus avancés.</p>\r\n\r\n<div style=\"text-align: center; margin-top: 30px;\">\r\n    <a href=\"#\" style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\r\n        Démarrer le prochain cours\r\n    </a>\r\n</div>\r\n', '../../../../public/image_and_video/mp4/HTML_3.mp4', '2024-08-26 10:32:22', 0),
(39, 30, 'Formatage du texte en HTML - Partie 1', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: Formatage de Texte en HTML - Partie 1</h2>\r\n\r\n<h3 style=\"color: #FF5722;\">Introduction</h3>\r\n\r\n<p>Le formatage du texte en HTML est essentiel pour donner du style et de l\'importance à votre contenu. Grâce à diverses balises HTML, vous pouvez mettre en gras, en italique, souligner, et bien plus encore. Dans cette première partie, nous allons explorer les balises de base pour le formatage du texte en HTML.</p>\r\n\r\n<h3 style=\"color: #FF5722;\">Mise en Gras et Italique</h3>\r\n\r\n<p>Les balises HTML vous permettent de mettre en gras ou en italique certains éléments de texte pour les rendre plus visibles ou pour accentuer un mot ou une phrase importante.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;strong&gt;</strong> : Utilisé pour mettre un texte en gras, indiquant une importance particulière.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;b&gt;</strong> : Met également en gras le texte, mais sans indiquer d\'importance supplémentaire.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;em&gt;</strong> : Utilisé pour mettre un texte en italique, soulignant une emphase.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;i&gt;</strong> : Met en italique le texte, mais sans signifier une emphase particulière.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Ce texte est &lt;strong&gt;très important&lt;/strong&gt; et ce texte est &lt;b&gt;en gras&lt;/b&gt;.&lt;/p&gt;\r\n&lt;p&gt;Ce texte est &lt;em&gt;emphatique&lt;/em&gt; et ce texte est &lt;i&gt;en italique&lt;/i&gt;.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Souligner et Barrer du Texte</h3>\r\n\r\n<p>Il est parfois utile de souligner ou de barrer du texte pour attirer l\'attention ou pour indiquer une correction ou suppression.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;u&gt;</strong> : Utilisé pour souligner le texte.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;s&gt;</strong> : Utilisé pour barrer le texte, indiquant que le contenu a été supprimé ou corrigé.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Ce texte est &lt;u&gt;souligné&lt;/u&gt; et ce texte est &lt;s&gt;barré&lt;/s&gt;.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Modification de la Taille du Texte</h3>\r\n\r\n<p>Vous pouvez ajuster la taille de votre texte pour le rendre plus visible ou pour hiérarchiser les informations. Bien que cela soit généralement géré par le CSS, HTML fournit quelques options pour modifier la taille du texte directement.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;small&gt;</strong> : Réduit la taille du texte.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;big&gt;</strong> : Augmente la taille du texte (balise dépréciée, non recommandée).</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Ce texte est &lt;small&gt;plus petit&lt;/small&gt; et ce texte est &lt;big&gt;plus grand&lt;/big&gt;.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<p>Cette première partie vous a introduit aux bases du formatage de texte en HTML. Passons maintenant à la deuxième partie où nous explorerons d\'autres options de formatage plus avancées.</p>\r\n\r\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\r\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Continuez à la partie 2 pour explorer davantage d\'options de formatage de texte en HTML.</p>\r\n\r\n<div style=\"text-align: center; margin-top: 30px;\">\r\n    <a href=\"#\" style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\r\n        Passer à la Partie 2\r\n    </a>\r\n</div>\r\n', '../../../../public/image_and_video/mp4/HTML_4.mp4', '2024-08-26 10:35:24', 0),
(40, 30, 'Formatage du texte en HTML - Partie 2', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: Formatage de Texte en HTML - Partie 2</h2>\r\n\r\n<h3 style=\"color: #FF5722;\">Modification de la Couleur du Texte</h3>\r\n\r\n<p>La couleur du texte peut être modifiée pour souligner des informations importantes ou pour s\'accorder avec le design global de la page. Bien que cela soit également géré principalement par le CSS, HTML permet de changer la couleur du texte directement via l\'attribut <code>style</code>.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p style=\"color: red;\"&gt;Ce texte est rouge.&lt;/p&gt;\r\n&lt;p style=\"color: blue;\"&gt;Ce texte est bleu.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Créer des Listes</h3>\r\n\r\n<p>Les listes sont un excellent moyen d\'organiser les informations. Il existe deux types principaux de listes en HTML : les listes non ordonnées (avec des puces) et les listes ordonnées (avec des numéros).</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;ul&gt;</strong> : Crée une liste non ordonnée.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;ol&gt;</strong> : Crée une liste ordonnée.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;li&gt;</strong> : Représente un élément de liste, utilisé à l\'intérieur des balises &lt;ul&gt; ou &lt;ol&gt;.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Liste Non Ordonnée :</strong></p>\r\n    <pre><code>&lt;ul&gt;\r\n    &lt;li&gt;Élément 1&lt;/li&gt;\r\n    &lt;li&gt;Élément 2&lt;/li&gt;\r\n    &lt;li&gt;Élément 3&lt;/li&gt;\r\n&lt;/ul&gt;</code></pre>\r\n</div>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Liste Ordonnée :</strong></p>\r\n    <pre><code>&lt;ol&gt;\r\n    &lt;li&gt;Premier élément&lt;/li&gt;\r\n    &lt;li&gt;Deuxième élément&lt;/li&gt;\r\n    &lt;li&gt;Troisième élément&lt;/li&gt;\r\n&lt;/ol&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Citation et Texte Préformaté</h3>\r\n\r\n<p>HTML offre des balises spécifiques pour citer des textes ou pour afficher du texte préformaté, c\'est-à-dire du texte qui conserve les espaces et les sauts de ligne tels qu\'ils apparaissent dans le code source.</p>\r\n\r\n<ul style=\"list-style-type: square; margin-left: 20px;\">\r\n    <li><strong style=\"color: #4CAF50;\">&lt;blockquote&gt;</strong> : Utilisé pour les longues citations.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;q&gt;</strong> : Utilisé pour les courtes citations intégrées dans un texte.</li>\r\n    <li><strong style=\"color: #4CAF50;\">&lt;pre&gt;</strong> : Affiche du texte préformaté, idéal pour le code ou les poèmes.</li>\r\n</ul>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Citation Longue :</strong></p>\r\n    <pre><code>&lt;blockquote&gt;\r\n    Ce texte est une citation longue. \r\n    Il est souvent utilisé pour des citations importantes ou des passages extraits de livres ou d\'articles.\r\n&lt;/blockquote&gt;</code></pre>\r\n</div>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple de Texte Préformaté :</strong></p>\r\n    <pre><code>&lt;pre&gt;\r\n    Voici un exemple de texte\r\n    qui conserve\r\n    les espaces et les sauts de ligne\r\n    tels qu\'ils apparaissent.\r\n&lt;/pre&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Conclusion</h3>\r\n\r\n<p>Vous avez maintenant une compréhension complète du formatage de texte en HTML. En combinant ces techniques, vous pouvez créer des pages web riches en contenu et bien structurées. Continuez à explorer ces balises pour les maîtriser et les utiliser efficacement dans vos projets.</p>\r\n\r\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\r\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Prêt à appliquer ce que vous avez appris? Commencez dès maintenant à expérimenter ces balises dans vos propres pages HTML.</p>\r\n\r\n<div style=\"text-align: center; margin-top: 30px;\">\r\n    <a href=\"#\" style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\r\n        Retour au Sommaire\r\n    </a>\r\n</div>\r\n', '../../../../public/image_and_video/mp4/HTML_5.mp4', '2024-08-26 10:50:24', 0),
(41, 30, 'Le formatage technique en HTML', '<h2 style=\"color: #4CAF50; text-align: center;\">Cours Détailé: Formatage Technique en HTML</h2>\r\n\r\n<h3 style=\"color: #FF5722;\">Introduction</h3>\r\n\r\n<p>Le formatage technique en HTML est crucial pour présenter du contenu technique, tel que du code, des commandes, ou des données structurées, de manière claire et lisible. HTML fournit plusieurs balises spécialement conçues pour formater ces types de contenu technique. Ce cours couvre les balises les plus importantes pour le formatage technique en HTML.</p>\r\n\r\n<h3 style=\"color: #FF5722;\">Balise &lt;code&gt; : Affichage de Code Inline</h3>\r\n\r\n<p>La balise <code>&lt;code&gt;</code> est utilisée pour afficher des extraits de code à l\'intérieur d\'une ligne de texte. Cette balise est idéale pour mettre en évidence des commandes ou des noms de variables sans interrompre le flux du texte.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Pour imprimer un message en Python, utilisez la commande &lt;code&gt;print&lt;/code&gt;. Par exemple : &lt;code&gt;print(\'Hello, world!\')&lt;/code&gt;.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balise &lt;pre&gt; : Texte Préformaté</h3>\r\n\r\n<p>La balise <code>&lt;pre&gt;</code> est utilisée pour afficher du texte préformaté. Le texte à l\'intérieur de cette balise conserve les espaces, les tabulations, et les sauts de ligne, ce qui en fait un excellent choix pour afficher du code source ou des données formatées.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;pre&gt;\r\ndef hello_world():\r\n    print(\"Hello, world!\")\r\n&lt;/pre&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balise &lt;kbd&gt; : Affichage de Commandes Clavier</h3>\r\n\r\n<p>La balise <code>&lt;kbd&gt;</code> est utilisée pour représenter les entrées au clavier. Cette balise est couramment utilisée pour indiquer des raccourcis clavier ou des commandes que l\'utilisateur doit entrer.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;Pour enregistrer votre travail, appuyez sur &lt;kbd&gt;Ctrl&lt;/kbd&gt; + &lt;kbd&gt;S&lt;/kbd&gt;.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balise &lt;samp&gt; : Affichage de Sorties de Code</h3>\r\n\r\n<p>La balise <code>&lt;samp&gt;</code> est utilisée pour représenter la sortie d\'un programme ou d\'une commande. Cette balise est idéale pour afficher ce que l\'utilisateur verrait dans un terminal ou une console après avoir exécuté un code.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;La commande affichera : &lt;samp&gt;Hello, world!&lt;/samp&gt;&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Balise &lt;var&gt; : Affichage de Variables</h3>\r\n\r\n<p>La balise <code>&lt;var&gt;</code> est utilisée pour représenter une variable dans une expression mathématique ou un code de programmation. Cette balise rend le texte en italique par défaut, indiquant qu\'il s\'agit d\'une variable ou d\'un paramètre.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #4CAF50; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;p&gt;La variable &lt;var&gt;x&lt;/var&gt; contient la valeur de l\'input utilisateur.&lt;/p&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Combinaison des Balises pour le Formatage Complexe</h3>\r\n\r\n<p>Dans certains cas, vous pouvez combiner plusieurs balises de formatage technique pour créer des documents techniques complexes. Par exemple, vous pourriez vouloir afficher du code avec des entrées clavier et des variables dans une même section.</p>\r\n\r\n<div style=\"background-color: #f9f9f9; border-left: 4px solid #FF5722; padding: 10px; margin: 20px 0;\">\r\n    <p><strong>Exemple :</strong></p>\r\n    <pre><code>&lt;pre&gt;\r\nPour définir une fonction en Python, utilisez la syntaxe suivante :\r\ndef &lt;var&gt;nom_fonction&lt;/var&gt;():\r\n    &lt;kbd&gt;Instruction&lt;/kbd&gt;\r\n    &lt;kbd&gt;Instruction&lt;/kbd&gt;\r\n&lt;/pre&gt;</code></pre>\r\n</div>\r\n\r\n<h3 style=\"color: #FF5722;\">Conclusion</h3>\r\n\r\n<p>Le formatage technique en HTML permet de présenter des informations complexes de manière claire et lisible. En maîtrisant ces balises, vous pourrez créer des documents techniques professionnels, des tutoriels, ou des guides de programmation qui seront facilement compréhensibles par vos utilisateurs.</p>\r\n\r\n<hr style=\"border: 0; height: 1px; background: #ccc; margin: 40px 0;\">\r\n<p style=\"text-align: center; font-size: 1.2em; color: #009688;\">Maintenant que vous comprenez le formatage technique en HTML, essayez d\'intégrer ces balises dans vos projets pour améliorer la présentation de vos contenus techniques.</p>\r\n\r\n<div style=\"text-align: center; margin-top: 30px;\">\r\n    <a href=\"#\" style=\"padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s;\">\r\n        Retour au Sommaire\r\n    </a>\r\n</div>\r\n', '../../../../public/image_and_video/mp4/HTML_6.mp4', '2024-08-26 11:01:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `biographie` text DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profils`
--

INSERT INTO `profils` (`id`, `utilisateur_id`, `prenom`, `nom`, `date_naissance`, `biographie`, `photo_profil`) VALUES
(1, 2, 'Abdurahman', 'USDI', '1995-04-20', 'Développeur web et web mobile', '1723992068__e1c4f4d4-4243-45de-afa2-737b6cc43dab.jpg'),
(2, 3, 'Abdurahman', 'USDI', '0000-00-00', 'Biographie d\'élève test développeur', NULL),
(3, 4, 'Abdurahman', 'Usdi', '1995-04-20', 'Eleve nouveau', '1724440353__e4de4719-ee6c-44b4-90f2-95c689c2c51f.jpg'),
(4, 5, 'Abdu', 'USDI', '1995-05-20', 'Formateur de E learning Abdu', '1724599316__e1c4f4d4-4243-45de-afa2-737b6cc43dab.jpg'),
(5, 6, 'Arcadia', 'Formateur', '2000-12-02', 'Test formateur', '1724606709_Fond D\'écran Gif.gif');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `quiz_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Administrateur'),
(2, 'Formateur'),
(3, 'Apprenant');

-- --------------------------------------------------------

--
-- Structure de la table `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `schedule` text NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `schedules`
--

INSERT INTO `schedules` (`id`, `teacher_id`, `schedule`, `assigned_at`) VALUES
(1, 1, 'rgzgzg', '2024-08-23 16:35:57');

-- --------------------------------------------------------

--
-- Structure de la table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `title`, `created_at`) VALUES
(30, 39, 'C&#039;est quoi HTML et CSS ? Et pourquoi l&#039;apprendre ?', '2024-08-25 22:59:57');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `cursus_valide` tinyint(1) DEFAULT 0,
  `certificate_issued` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `cursus_valide`, `certificate_issued`, `created_at`) VALUES
(1, 'Abdurahman', 'admin.e-learning@gmail.com', '$2y$10$jsZ/YfANY7FCgrDGBpX0Vu5iTa4NfrdnSLr1CiVmsEMJnSt3K17nW', 2, 0, 0, '2024-08-18 11:49:10'),
(2, 'Administrateur', 'abdu.usdi@hotmail.fr', '$2y$10$Urmd3/DRntRF7WMQjiUuAuh6R0OK3obEQs79Vo.r74BlMbaFi29Q6', 1, 0, 0, '2024-08-18 11:51:11'),
(3, 'eleve', 'Abdu.usdi@gmail.com', '$2y$10$Eqhi.ryfTfovRfrWULHF0ujfZzWDFeshpLEoIAsJUNyzubYxcQVMO', 3, 0, 1, '2024-08-18 11:54:13'),
(4, 'AbduEleve', 'usdiabdu@gmail.com', '$2y$10$8X7lRWEB5ghBmU31OpTivOnKRU8tHaVIr7Pc0FlHIcDj52V.mk0.a', 3, 0, 1, '2024-08-23 16:48:41'),
(5, 'Formateur', 'formateur.e-learning@gmail.com', '$2y$10$F1v6eaHoyrG3TKnR830sz.PdyAKQaITCfhO/TkIy2/8tpRji66.Bm', 2, 0, 0, '2024-08-25 14:57:18'),
(6, 'arcadia', 'admin.arcadia@gmail.com', '$2y$10$RpDcmLzu3R5LTWdAnmrOX.1hVAvG3g4GcNzFw05XrszCIfH6tNgJO', 2, 0, 0, '2024-08-25 17:21:53');

-- --------------------------------------------------------

--
-- Structure de la table `user_formations`
--

CREATE TABLE `user_formations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_formations`
--

INSERT INTO `user_formations` (`id`, `user_id`, `formation_id`) VALUES
(11, 3, 18),
(12, 4, 18);

-- --------------------------------------------------------

--
-- Structure de la table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL,
  `sub_category_id` int(11) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categories_formation_id` (`formation_id`);

--
-- Index pour la table `category_progress`
--
ALTER TABLE `category_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `exam_feedback`
--
ALTER TABLE `exam_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Index pour la table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Index pour la table `formations`
--
ALTER TABLE `formations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Index pour la table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Index pour la table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `lives`
--
ALTER TABLE `lives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Index pour la table `profils`
--
ALTER TABLE `profils`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Index pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quizzes_formation_id` (`formation_id`);

--
-- Index pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Index pour la table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `user_formations`
--
ALTER TABLE `user_formations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `formation_id` (`formation_id`);

--
-- Index pour la table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `formation_id` (`formation_id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `page_id` (`page_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;
--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `category_progress`
--
ALTER TABLE `category_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `exam_feedback`
--
ALTER TABLE `exam_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `formations`
--
ALTER TABLE `formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `lives`
--
ALTER TABLE `lives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `profils`
--
ALTER TABLE `profils`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT pour la table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user_formations`
--
ALTER TABLE `user_formations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_formation_id` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `category_progress`
--
ALTER TABLE `category_progress`
  ADD CONSTRAINT `category_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_progress_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exam_feedback`
--
ALTER TABLE `exam_feedback`
  ADD CONSTRAINT `exam_feedback_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `exam_submissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `exam_submissions`
--
ALTER TABLE `exam_submissions`
  ADD CONSTRAINT `exam_submissions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `lives`
--
ALTER TABLE `lives`
  ADD CONSTRAINT `fk_utilisateur_id` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profils`
--
ALTER TABLE `profils`
  ADD CONSTRAINT `profils_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_quizzes_formation_id` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_formations`
--
ALTER TABLE `user_formations`
  ADD CONSTRAINT `user_formations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_formations_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`formation_id`) REFERENCES `formations` (`id`),
  ADD CONSTRAINT `user_progress_ibfk_3` FOREIGN KEY (`sub_category_id`) REFERENCES `subcategories` (`id`),
  ADD CONSTRAINT `user_progress_ibfk_4` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
