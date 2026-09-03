<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);

    // Get current image
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $profileImage = $user['profile_image'];

    // Upload new image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {

        $allowed = ['jpg','jpeg','png','gif'];

        $fileName = $_FILES['profile_image']['name'];
        $tmpName = $_FILES['profile_image']['tmp_name'];

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($extension, $allowed)) {

            $newName = time() . "_" . uniqid() . "." . $extension;

            move_uploaded_file(
                $tmpName,
                "uploads/profile/" . $newName
            );

            $profileImage = $newName;
        }
    }

    // Update database
    $stmt = $conn->prepare("
        UPDATE users
        SET
            fullname = ?,
            email = ?,
            profile_image = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $fullname,
        $email,
        $profileImage,
        $id
    ]);

    // Update session name
    $_SESSION['fullname'] = $fullname;

    header("Location: profile.php?success=1");
    exit();
}
?>