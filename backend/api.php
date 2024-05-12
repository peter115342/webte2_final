<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
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
switch ($method) {
    case 'GET':
        // QUESTION BY CODE
        if (preg_match("/^\/([a-zA-Z0-9]{5})$/", $endpoint, $matches)) {
            $code = $matches[1];
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
        // GET ALL QUESTIONS BY USER
        if (preg_match("/^\/question\/user\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $questions = $questionObject->getQuestionsByUserId($id, $data);
            if($questions){
                echo json_encode($questions);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        // GET ALL ANSWERS TO QUESTION
        elseif (preg_match("/^\/question\/(\d+)\/answers$/", $endpoint, $matches)) {
            $question_id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $answers = $questionObject->getAllQuestionAnswers($question_id, $data);
            if($answers){
                echo json_encode($answers);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        // QUESTION BY ID
        elseif (preg_match("/^\/question\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $data = json_decode(file_get_contents("php://input"), true);
            $question = $questionObject->getQuestionById($id, $data);
            if($question){
                echo json_encode($question);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
         // GET ALL USERS
        elseif ($endpoint ===  "/user/list") {
            $data = json_decode(file_get_contents("php://input"), true);
            $users = $userObject->getAllUsers($data);
            if($users){
                echo json_encode($users);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
         // LOGIN USER
        elseif ($endpoint ===  "/user/login") {
            $data = json_decode(file_get_contents("php://input"), true);
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(["message" => "Error"]);
                exit();
            }
            $user = $userObject->login($data);           
            if($user){
                echo json_encode($user);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
        // CREATE ANSWER
        elseif ($endpoint === "/answer") {
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
        }
        // CHECK USERNAME
        elseif ($endpoint === "/user/username") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->checkUsername($data);
            if($result){
                echo json_encode(["message" => "OK"]);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }
         // CHECK ACCESS TOKEN
         elseif ($endpoint === "/user/access") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->checkAcessToken($data);
            if($result){
                echo json_encode($result);
                http_response_code(200);
            } else {
                echo json_encode(["message" => "Error"]);
                http_response_code(400);
            }
        }  
        
         // LOGOUT
         elseif ($endpoint === "/user/logout") {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->logout($data);
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
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->deleteAnswer($id, $data);
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
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $questionObject->deleteQuestion($id, $data);
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
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $userObject->deleteUser($id, $data);
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