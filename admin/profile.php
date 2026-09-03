<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch logged-in user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">
    <i class="fa-solid fa-user"></i> My Profile
</h2>

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success alert-dismissible fade show">

    Profile updated successfully.

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>

<div class="card shadow">

    <div class="card-body">

        <form
            action="update_profile.php"
            method="POST"
            enctype="multipart/form-data">

            <input
                type="hidden"
                name="id"
                value="<?= $user['id']; ?>">

            <div class="row">

                <div class="col-md-3 text-center">

                    <?php
                    $image = !empty($user['profile_image'])
                        ? "uploads/profile/".$user['profile_image']
                        : "https://via.placeholder.com/180?text=Profile";
                    ?>

                    <img
                        src="<?= $image; ?>"
                        class="img-fluid rounded-circle border mb-3"
                        style="width:180px;height:180px;object-fit:cover;">

                    <input
                        type="file"
                        name="profile_image"
                        class="form-control">

                </div>

                <div class="col-md-9">

                    <div class="mb-3">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="fullname"
                            class="form-control"
                            value="<?= htmlspecialchars($user['fullname']); ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($user['email']); ?>"
                            required>

                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <label class="form-label">
                                Role
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= ucfirst($user['role']); ?>"
                                readonly>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="<?= $user['status']; ?>"
                                readonly>

                        </div>

                    </div>

                    <div class="mt-3">

                        <button class="btn btn-success">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Profile

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>