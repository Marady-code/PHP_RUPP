<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-KEY");

// Handle OPTIONS method for CORS pre-flight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Fix include paths with proper directory
$currentDir = dirname(__FILE__);
include_once $currentDir . '/../config/database.php';
include_once $currentDir . '/../models/Patient.php';
include_once $currentDir . '/../utils/logger.php';

// Simple API Key Authentication
$apiKey = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';
$validApiKey = 'SECRET_API_KEY_123'; // Match the key in the frontend

if ($apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized. Invalid API Key."));
    exit;
}

// Make sure the database connection is initialized
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(array(
        "message" => "Database connection failed. Please check your configuration.",
        "status" => "error"
    ));
    exit;
}

$patient = new Patient($db);

// Already handled the database connection above

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Read single or all patients
        if(isset($_GET['id'])) {
            $patient->id = $_GET['id'];
            $patient->readOne();
            
            if($patient->first_name != null) {
                $patient_arr = array(
                    "id" => $patient->id,
                    "first_name" => $patient->first_name,
                    "last_name" => $patient->last_name,
                    "email" => $patient->email,
                    "phone" => $patient->phone,
                    "address" => $patient->address,
                    "date_of_birth" => $patient->date_of_birth,
                    "gender" => $patient->gender,
                    "blood_type" => $patient->blood_type
                );
                http_response_code(200);
                echo json_encode($patient_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Patient not found."));
            }
        } else {
            $stmt = $patient->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $patients_arr = array();
                $patients_arr["records"] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $patient_item = array(
                        "id" => $id,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "email" => $email,
                        "phone" => $phone,
                        "address" => $address,
                        "date_of_birth" => $date_of_birth,
                        "gender" => $gender,
                        "blood_type" => $blood_type
                    );
                    
                    array_push($patients_arr["records"], $patient_item);
                }
                
                http_response_code(200);
                echo json_encode($patients_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No patients found."));
            }
        }
        break;
          case 'POST':
        // Create new patient
        try {
            $raw_data = file_get_contents("php://input");
            error_log("Raw POST data received: " . $raw_data);
            
            $data = json_decode($raw_data);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
                http_response_code(400);
                echo json_encode(array(
                    "message" => "Invalid JSON format: " . json_last_error_msg(),
                    "status" => "error"
                ));
                break;
            }
            
            if(
                !empty($data->first_name) &&
                !empty($data->last_name) &&
                !empty($data->email)
            ) {
                $patient->first_name = $data->first_name;
                $patient->last_name = $data->last_name;
                $patient->email = $data->email;
                $patient->phone = $data->phone ?? "";
                $patient->address = $data->address ?? "";
                $patient->date_of_birth = $data->date_of_birth ?? "";
                $patient->gender = $data->gender ?? "";
                $patient->blood_type = $data->blood_type ?? "";
                
                if($patient->create()) {
                    http_response_code(201);
                    echo json_encode(array(
                        "message" => "Patient was created successfully.",
                        "status" => "success"
                    ));
                } else {
                    error_log("Database error creating patient");
                    http_response_code(503);
                    echo json_encode(array(
                        "message" => "Unable to create patient. Database error.",
                        "status" => "error"
                    ));
                }
            } else {
                error_log("Incomplete patient data");
                http_response_code(400);
                echo json_encode(array(
                    "message" => "Unable to create patient. Data is incomplete.",
                    "status" => "error",
                    "missing_fields" => array_filter([
                        empty($data->first_name) ? "first_name" : null,
                        empty($data->last_name) ? "last_name" : null,
                        empty($data->email) ? "email" : null
                    ])
                ));
            }
        } catch (Exception $e) {
            error_log("Exception in patient creation: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(array(
                "message" => "Server error occurred.",
                "status" => "error"
            ));
        }
        break;
        
    case 'PUT':
        // Update patient
        $data = json_decode(file_get_contents("php://input"));
        
        $patient->id = $data->id;
        
        $patient->first_name = $data->first_name;
        $patient->last_name = $data->last_name;
        $patient->email = $data->email;
        $patient->phone = $data->phone ?? null;
        $patient->address = $data->address ?? null;
        $patient->date_of_birth = $data->date_of_birth ?? null;
        $patient->gender = $data->gender ?? null;
        $patient->blood_type = $data->blood_type ?? null;
        
        if($patient->update()) {
            http_response_code(200);
            echo json_encode(array("message" => "Patient was updated."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update patient."));
        }
        break;
        
    case 'DELETE':
        // Soft delete patient
        $data = json_decode(file_get_contents("php://input"));
        
        $patient->id = $data->id;
        
        if($patient->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Patient was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete patient."));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>