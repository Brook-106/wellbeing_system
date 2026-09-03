<?php
require_once "../includes/session.php";
include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Add Department</h2>

<div class="card shadow">

    <div class="card-body">

        <form action="insert_department.php" method="POST">

            <div class="mb-3">

                <label class="form-label">Department Name</label>

                <input
                    type="text"
                    name="department_name"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">Department Code</label>

                <input
                    type="text"
                    name="department_code"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">HOD Name</label>

                <input
                    type="text"
                    name="hod_name"
                    class="form-control">

            </div>

            <button class="btn btn-success">
                Save Department
            </button>

            <a href="departments.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>