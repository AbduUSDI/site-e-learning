<?php
namespace App\Controllers;

use FPDF;
use App\Models\User;
use App\Models\Formation;

class CertificateController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function generateCertificate($userId)
    {
        $userModel = new User($this->db);
        $user = $userModel->findById($userId);

        if (!$user || $user['cursus_valide'] != 1) {
            throw new \Exception("Cursus non validé ou utilisateur introuvable.");
        }

        $formationModel = new Formation($this->db);
        $formation = $formationModel->getFormationByUserId($userId);

        if (!$formation || !isset($formation['name'])) {
            throw new \Exception("Formation introuvable ou nom de la formation non défini.");
        }

        $pdf = new FPDF();
        $pdf->AddPage();
        $this->drawBorder($pdf);
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor(50, 50, 100); // Couleur bleu foncé
        $pdf->Cell(0, 20, $this->convertToUtf8('CERTIFICAT D\'ACHÈVEMENT'), 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 16);
        $pdf->SetTextColor(0, 0, 0); // Couleur noire
        $pdf->Cell(0, 10, $this->convertToUtf8('Ce certificat est décerné à :'), 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(0, 102, 204); // Couleur bleu clair
        $pdf->Cell(0, 10, $this->convertToUtf8($user['username']), 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 16);
        $pdf->SetTextColor(0, 0, 0); // Couleur noire
        $pdf->MultiCell(0, 10, $this->convertToUtf8('Pour avoir complété le cursus de la formation ' . $formation['name']), 0, 'C');
        $pdf->Ln(20);
        $pdf->Cell(0, 10, 'Date d\'obtention : ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(30);
        $pdf->SetFont('Arial', '', 16);
        $pdf->Cell(0, 10, 'Signature', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 10, '________________________', 0, 1, 'C');
        $rootPath = $_SERVER['DOCUMENT_ROOT'] . '/e_learning/';
        $certificatesDir = $rootPath . 'public/uploads/certificates/';
        
        if (!file_exists($certificatesDir)) {
            mkdir($certificatesDir, 0777, true);
        }

        $filename = $certificatesDir . 'certificat_' . $userId . '.pdf';
        $pdf->Output('F', $filename);
        $userModel->updateCertificateIssued($userId);
        return $filename;
    }

    private function convertToUtf8($text)
    {
        return html_entity_decode(mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8'), ENT_QUOTES, 'ISO-8859-1');
    }

    private function drawBorder($pdf)
    {
        // Couleur de la bordure
        $pdf->SetDrawColor(0, 102, 204); // Bleu clair
        $pdf->SetLineWidth(2);
        $pdf->Rect(10, 10, 190, 277, 'D');
    }
}
