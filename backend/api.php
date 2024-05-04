<?php

require_once "User.php";
require_once "Question.php";
require_once "config.php";
error_reporting(E_ALL);    // TODO: Odstranit
ini_set('display_errors', 1);
$userObject = new User($conn);
$questionObject = new Question($conn);

$method = $_SERVER['REQUEST_METHOD'];
$queryString = $_SERVER['QUERY_STRING'];
parse_str($queryString, $params);
$endpoint = $params['endpoint'];


$endpoint = '/' . $endpoint;
header('Content-Type: application/json');
switch ($method) {
    case 'GET':
        // GET USER
        if ($endpoint ===  "/user/login") {
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(["message" => "Error"]);
                exit(); 
            }            
            $user = $userObject->getUser($data);
            http_response_code(200);
            echo json_encode($user);
        }  
        // GET ALL USERS
        elseif ($endpoint ===  "/user") {
           $users = $userObject->getAllUsers();
           http_response_code(200);
           echo json_encode($users);
        }  
        // GET ALL QUESTIONS BY USER
        elseif (preg_match("/^\/question\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $questions = $questionObject->getQuestionsByUserId($id);
            http_response_code(200);
            echo json_encode($questions);
        }
        // QUESTION BY ID
        elseif (preg_match("/^\/question\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $question = $questionObject->getQuestionById($id);
            http_response_code(200);
            echo json_encode($question);
        }
        
        // QUESTION BY CODE
        elseif (preg_match("/^\/([a-zA-Z0-9]{5})$/", $endpoint, $matches)) {
            echo "imhere";
            $code = $matches[1]; 
            echo $code;
            $question = $questionObject->getQuestionByCode($code);
            http_response_code(200);
            echo json_encode($question);
        } 
        else {
            http_response_code(400);
            echo json_encode(["message" => "Bad request"]);
        }
        break;
    case 'POST':
        // CREATE ANSWER 
        if ($endpoint === "/answer") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->createAnswer($data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        } 
        // CREATE QUESTION
        elseif ($endpoint === "/question") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->createQuestion($data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        } 
        // CREATE USER
        elseif ($endpoint === "/user") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->createUser($data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Bad request"]);
        }
        break;
    case 'PUT':
        // UPDATE ANSWER
        if (preg_match("/^\/answer\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->updateAnswer($id, $data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        } 
        // UPDATE QUESTION 
        elseif (preg_match("/^\/question\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->updateQuestion($id, $data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        // UPDATE USER
        elseif (preg_match("/^\/user\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->updateUser($id, $data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Bad request"]);
        }
        break;
    case 'DELETE':
        // DELETE ANSWER
        if (preg_match("/^\/answer\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $result = $questionObject->deleteAnswer($id);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        } 
        // DELETE QUESTION
        elseif (preg_match("/^\/question\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $result = $questionObject->deleteQuestion($id);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        // DELETE USER
        elseif (preg_match("/^\/user\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $result = $userObject->deleteUser($id);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        else {
            http_response_code(400);
            echo json_encode(["message" => "Bad request"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);

        
    }  