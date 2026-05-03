<?php
session_start();

require_once __DIR__ . '/../../app/core/database.php';
require_once __DIR__ . '/../../app/controllers/EnrollmentController.php';

$db = (new Database())->connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$enrollmentController = new EnrollmentController($db);

/* GET STUDENT ID */
$stmt = $db->prepare("
    SELECT student_id, first_name, last_name, email, phone_number, address
    FROM students
    WHERE user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found.");
}

$studentId = $student['student_id'];

/* HANDLE ACTIONS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['create_enrollment'])) {
        $enrollmentController->enrollStudent([
            'student_id' => $studentId,
            'school_year' => $_POST['school_year'],
            'grade_level' => $_POST['grade_level'],
            'section' => $_POST['section'],
            'enrollment_type' => $_POST['enrollment_type']
        ]);
    }

    if (isset($_POST['submit_enrollment'])) {
        $enrollmentController->submitEnrollment($studentId);
    }

    if (isset($_POST['upload_doc'])) {
        $enrollmentController->uploadDocument(
            $studentId,
            $_FILES['document'],
            $_POST['doc_type']
        );
    }
}

/* LOAD DATA */
$data = $enrollmentController->getStudentDashboard($studentId);

$enrollment = $data['enrollment'];
$missing = $data['missing_documents'];

/* USER INFO (NO PROFILE UPLOAD HERE) */
$stmt = $db->prepare("
    SELECT u.username, p.display_name, p.profile_picture
    FROM users u
    LEFT JOIN user_profiles p ON u.user_id = p.user_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* SUBMITTED DOCS */
$submitted = [];
if ($enrollment) {
    $stmt = $db->prepare("
        SELECT document_type, file_path 
        FROM documents 
        WHERE enrollment_id = ?
    ");
    $stmt->bind_param("i", $enrollment['enrollment_id']);
    $stmt->execute();
    $submitted = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/cherry.css">
</head>

<body class="admin-dashboard">

<div class="sidebar">
    <h2>Hello, <?php echo $user['display_name'] ?? $user['username']; ?></h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>

    <form method="POST" action="/queueless/public/logout.php">
        <button>Logout</button>
    </form>
</div>

<div class="dashboard-main">

    <div class="topbar">
        <h1>Student Dashboard</h1>
    </div>

    <div class="cards">

        <!-- PROFILE (READ ONLY, NO UPLOAD HERE) -->
        <div class="card profile-card">

            <?php if (!empty($user['profile_picture'])): ?>
                <img src="../../public/<?php echo $user['profile_picture']; ?>">
            <?php else: ?>
                <img src="../../public/assets/images/default.png">
            <?php endif; ?>

            <h3><?php echo $user['display_name'] ?? $user['username']; ?></h3>

            <a href="profile.php" class="btn-apply">Edit Profile</a>

            <div class="enrollment-personal-info">

                <h4>Personal Information</h4>

                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">
                        <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value"><?php echo $student['email']; ?></span>
                </div>

                <div class="info-row">
                    <span class="label">Contact:</span>
                    <span class="value"><?php echo $student['phone_number']; ?></span>
                </div>

                <div class="info-row">
                    <span class="label">Address:</span>
                    <span class="value"><?php echo $student['address']; ?></span>
                </div>

            </div>

        </div>

        <!-- ENROLLMENT -->
        <div class="card">

            <?php if (!$enrollment): ?>

                <h3>Create Enrollment</h3>

                <form method="POST">
                    <input type="text" name="school_year" placeholder="School Year" required>
                    <input type="text" name="grade_level" placeholder="Grade Level" required>
                    <input type="text" name="section" placeholder="Section" required>

                    <select name="enrollment_type" required>
                        <option value="new">New</option>
                        <option value="transfer">Transfer</option>
                        <option value="old">Old</option>
                    </select>

                    <button name="create_enrollment">Create</button>
                </form>

            <?php else: ?>

                <h3>Enrollment Details</h3>

                <div class="file-box"><span class="file-name">School Year: <?php echo $enrollment['school_year']; ?></span></div>
                <div class="file-box"><span class="file-name">Grade: <?php echo $enrollment['grade_level']; ?></span></div>
                <div class="file-box"><span class="file-name">Section: <?php echo $enrollment['section']; ?></span></div>
                <div class="file-box"><span class="file-name">Type: <?php echo strtoupper($enrollment['enrollment_type']); ?></span></div>
                <div class="file-box"><span class="file-name">Status: <?php echo ucfirst($enrollment['status']); ?></span></div>

                <hr>

                <h3>Submitted Documents</h3>

                <?php if (!empty($submitted)): ?>
                    <?php foreach ($submitted as $doc): ?>
                        <div class="file-box">
                            <span class="file-name">
                                <?php echo strtoupper($doc['document_type']); ?> - SUBMITTED
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No documents submitted yet.</p>
                <?php endif; ?>

                <?php if ($enrollment['status'] !== 'submitted' && !empty($missing)): ?>

                    <h3>Missing Documents</h3>

                    <?php if (!empty($missing)): ?>
                        <?php foreach ($missing as $doc): ?>
                            <form method="POST" enctype="multipart/form-data">

                                <div class="file-box">
                                    <label class="file-btn">
                                        Upload
                                        <input type="file" name="document" required>
                                    </label>

                                    <span class="file-name">
                                        <?php echo strtoupper($doc); ?>
                                    </span>
                                </div>

                                <input type="hidden" name="doc_type" value="<?php echo $doc; ?>">

                                <button name="upload_doc">Submit</button>
                            </form>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST">
                        <button name="submit_enrollment">Submit Enrollment</button>
                    </form>

                <?php else: ?>
                    <p>Enrollment locked. No further changes allowed.</p>
                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>
</div>

</body>
</html>