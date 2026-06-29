<?php
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        exit(0); 
    }

    //require_once 'db.php';

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $stmt = $mysqli->prepare("SELECT 
                e.matricule AS eMatr,
                e.nom AS eNom, 
                e.prenom AS ePrenom, 
                e.niveau AS eNiveau,
                e.parcours AS eParcours,
                e.email AS eEmail, 
                s.annee_univ AS sAnnee, 
                s.note AS sNote,
                s.design AS sDesign
              FROM ETUDIANTS e
              LEFT JOIN SOUTENIR s ON s.matr = e.matr");

        if($stmt->execute()){
            $result = $stmt->get_result();
            $students = [];

            while($row = $result->fetch_assoc()){
                $students[] = [
                    'matr' => $row['eMatr'],
                    'name' => $row['eNom'],
                    'fstName' => $row['ePrenom'],
                    'level' => $row['eNiveau'],
                    'class' => $row['eParcours'],
                    'email' => $row['eEmail'],
                    'years' => $row['sAnnee'],
                    'score' => $row['sNote'],
                    'status' => $row['sDesign']
                ];
            }

            echo json_encode([
                'status' => 'success',
                'data'=> $students
            ]);
        }else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Erreur: ' . $mysqli->error
            ]);
        }
    }

?>