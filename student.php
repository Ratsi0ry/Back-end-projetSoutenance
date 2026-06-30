<?php
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        exit(0); 
    }

    //require_once 'db.php';

    //modification
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'update'){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if(!empty($data['matr']) && !empty($data['name']) && !empty($data['fstName']) &&!empty($data['level']) && !empty($data['class']) 
        &&!empty($data['email']) && !empty($data['years']) && !empty($data['score']) && !empty($data['status']))
        {
            try{
                $mysqli->begin_transaction();


                $stmt1 = $mysqli->prepare("UPDATE ETUDIANTS SET 
                    matricule = ?,
                    nom = ?,
                    prenom = ?,
                    niveau = ?,
                    parcours = ?,
                    email = ?
                WHERE matricule = ?");

                $stmt1->bind_param("sssssss", $data['matr'], $data['name'], $data['fstName'], $data['level'], $data['class'], $data['email'], $data['matr']);
                $stmt1->execute();
                $stmt1->close();

                $stmt2 = $mysqli->prepare("UPDATE SOUTENIR SET
                annee_univ = ?,
                note = ?,
                design = ?
                WHERE matr = ?");

                $stmt2->bind_param("siss", $data['years'], $data['score'], $data['status'], $data['matr']);
                $stmt2->execute();
                $stmt2->close();

                $mysqli->commit();

                echo json_encode([
                    'status' => 'success',
                    'message' => "Les informations sur l'etudiant ont été modifiées"
                ]);
            }catch(Exception $e){
                $mysqli->rollback();
                echo json_encode([
                   'status' => 'error',
                   'message' => 'Erreur de mis a jour'.$e->getMessage() 
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Impossible de changer les donnees'
            ]);
        }
    }

    //suppression
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'delete'){
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if(!empty($data['matr'])){
            $stmt = $mysqli->prepare("DELETE FROM ETUDIANTS WHERE matricule = ?");
            $stmt->bind_param("s", $data['matr']);

            if($stmt->execute()){
                echo json_encode([
                    'status' => 'success',
                    'message' => "L'etudiant a été supprimer du base de donnéé"
                ]);
            }else{
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erreur'.$stmt->error
                ]);
            }
            $stmt->close();
        } else{
            echo json_encode([
                'status' => 'error',
                'message' => "Impossible d'effectuer l'operation de suppréssion"
            ]);
        }
    }

?>