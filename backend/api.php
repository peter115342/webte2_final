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
        // GET ALL USERS
        if ($endpoint ===  "/user") {
            $users = $userObject->getAllUsers();
            http_response_code(200);
            echo json_encode($users);
        }
        // GET ALL QUESTIONS BY USER
        elseif (preg_match("/^\/question\/user\/(\d+)$/", $endpoint, $matches)) {
            $id = $matches[1];
            $questions = $questionObject->getQuestionsByUserId($id);
            http_response_code(200);
            echo json_encode($questions);
        }
        // GET ALL ANSWERS TO QUESTION
        elseif (preg_match("/^\/question\/(\d+)\/answers$/", $endpoint, $matches)) {
            $question_id = $matches[1];
            $answers = $questionObject->getAllQuestionAnswers($question_id);
            http_response_code(200);
            echo json_encode($answers);
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
         // LOGIN USER
         if ($endpoint ===  "/user/login") {
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
        // FIND USER
        elseif ($endpoint === "/user/username") {
            $data = json_decode(file_get_contents("php://input"), true);
            $username = $userObject->findUser($data);
            if($username){
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
