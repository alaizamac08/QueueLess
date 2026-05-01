<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../core/database.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Guardian.php";
require_once __DIR__ . "/../models/Enrollment.php";
require_once __DIR__ . "/../models/StudentRequirement.php";

/* -------------------------
   DATABASE CONNECTION
-------------------------- */
$db = (new Database())->connect();

/* -------------------------
   MODELS
-------------------------- */
$student = new Student($db);
$guardian = new Guardian($db);
$enrollment = new Enrollment($db);
$studentRequirement = new StudentRequirement($db);

$user_id = $_SESSION['user_id'];

// get staff_id from user_id
$stmt = $db->prepare("SELECT staff_id FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("No staff record found");
}

$staff_id = $row['staff_id'];

/* -------------------------
   FILE UPLOAD FUNCTION
-------------------------- */
function uploadFile($file, $uploadDir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return null;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid("doc_", true) . "." . $ext;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fullPath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return "uploads/documents/enrollments/" . $fileName;
    }

    return null;
}

$uploadDir = __DIR__ . "/../../public/uploads/documents/enrollments/";

/* -------------------------
   GUARDIAN INSERT
-------------------------- */
$guardianData = [
    "first_name" => $_POST['g_first_name'],
    "last_name" => $_POST['g_last_name'],
    "relationship" => $_POST['relationship'],
    "occupation" => $_POST['occupation'],
    "contact_number" => $_POST['contact_number']
];

if (!$guardian->createGuardian($guardianData)) {
    die("Guardian insert failed");
}

$guardian_id = $db->insert_id;

/* -------------------------
   STUDENT INSERT
-------------------------- */
$studentData = [
    "lrn" => $_POST['lrn'],
    "first_name" => $_POST['first_name'],
    "middle_name" => $_POST['middle_name'],
    "last_name" => $_POST['last_name'],
    "suffix" => $_POST['suffix'],
    "sex" => $_POST['sex'],
    "birth_date" => $_POST['birth_date'],
    "age" => $_POST['age'],
    "place_of_birth" => $_POST['place_of_birth'],
    "nationality" => $_POST['nationality'],
    "address" => $_POST['address'],
    "phone_number" => $_POST['phone_number'],
    "email" => $_POST['email']
];

if (!$student->createStudent($studentData)) {
    die("Student insert failed");
}

$student_id = $db->insert_id;

/* -------------------------
   LINK STUDENT ↔ GUARDIAN
-------------------------- */
$stmt = $db->prepare("
    INSERT INTO student_guardians (student_id, guardian_id)
    VALUES (?, ?)
");

$stmt->bind_param("ii", $student_id, $guardian_id);

if (!$stmt->execute()) {
    die("Student-Guardian link failed");
}

/* -------------------------
   ENROLLMENT INSERT
-------------------------- */
$enrollmentData = [
    "student_id" => $student_id,
    "school_year" => $_POST['school_year'],
    "grade_level" => $_POST['grade_level'],
    "section" => $_POST['section'],
    "enrollment_type" => $_POST['enrollment_type'],
    "status" => "pending",
    "processed_by" => $user_id
];

if (!$user_id) {
    die("Invalid user session");
}

$enrollment_id = $enrollment->createEnrollment($enrollmentData);

if (!$enrollment_id) {
    die("Enrollment insert failed");
}

/* -------------------------
   SAVE REQUIREMENTS
-------------------------- */
function saveRequirement($model, $enrollment_id, $requirement_id, $file, $uploadDir, $staff_id)
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return;

    $filePath = uploadFile($file, $uploadDir);
    if (!$filePath) return;

    if (!$model->addStudentRequirement(
        $enrollment_id,
        $requirement_id,
        "Submitted",
        date("Y-m-d"),
        $staff_id,
        $filePath
    )) {
        die("Requirement insert failed");
    }
}

/* -------------------------
   REQUIREMENTS
-------------------------- */
saveRequirement($studentRequirement, $enrollment_id, 1, $_FILES['birth_certificate'], $uploadDir, $staff_id);
saveRequirement($studentRequirement, $enrollment_id, 2, $_FILES['report_card'], $uploadDir, $staff_id);
saveRequirement($studentRequirement, $enrollment_id, 3, $_FILES['good_moral_certificate'], $uploadDir, $staff_id);

echo "Enrollment successfully submitted";
?>