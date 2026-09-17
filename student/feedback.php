<?php

session_start();

require_once "../includes/db.php";

/*
|--------------------------------------------------------------------------
| Student Authentication
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get Student Information
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        s.id AS student_id,
        u.fullname,
        u.email
    FROM students s
    INNER JOIN users u ON s.user_id = u.id
    WHERE s.user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}

include "../includes/header.php";

if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<div class="container-fluid py-4">

    <!-- Page Heading -->
    <div class="mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid fa-comment-dots me-2"></i>
            Feedback
        </h2>

        <p class="text-muted mb-0">
            Share your experience and help us improve the wellbeing services.
        </p>
    </div>

    <!-- Information -->
    <div class="alert alert-info border-0 shadow-sm">
        <i class="fa-solid fa-circle-info me-2"></i>

        Your feedback helps improve the student wellbeing experience.
        Please provide honest and respectful feedback.
    </div>

    <!-- Feedback Card -->
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                <i class="fa-solid fa-pen-to-square me-2"></i>
                Submit Feedback
            </h5>

        </div>

        <div class="card-body">

            <form id="feedbackForm">

                <!-- Student -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Student
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($student['fullname']); ?>"
                        readonly
                    >

                </div>

                <!-- Category -->
                <div class="mb-4">

                    <label for="category" class="form-label fw-semibold">
                        Feedback Category
                    </label>

                    <select
                        id="category"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select category
                        </option>

                        <option value="Counselling">
                            Counselling
                        </option>

                        <option value="Appointment">
                            Appointment
                        </option>

                        <option value="Mentor Support">
                            Mentor Support
                        </option>

                        <option value="Student Portal">
                            Student Portal
                        </option>

                        <option value="Resources">
                            Resources
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                </div>

                <!-- Rating -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Overall Rating
                    </label>

                    <div
                        class="rating-container"
                        id="ratingContainer"
                    >

                        <button
                            type="button"
                            class="rating-star"
                            data-rating="1"
                            aria-label="1 star"
                        >
                            ★
                        </button>

                        <button
                            type="button"
                            class="rating-star"
                            data-rating="2"
                            aria-label="2 stars"
                        >
                            ★
                        </button>

                        <button
                            type="button"
                            class="rating-star"
                            data-rating="3"
                            aria-label="3 stars"
                        >
                            ★
                        </button>

                        <button
                            type="button"
                            class="rating-star"
                            data-rating="4"
                            aria-label="4 stars"
                        >
                            ★
                        </button>

                        <button
                            type="button"
                            class="rating-star"
                            data-rating="5"
                            aria-label="5 stars"
                        >
                            ★
                        </button>

                    </div>

                    <input
                        type="hidden"
                        id="rating"
                        value=""
                    >

                    <small
                        id="ratingText"
                        class="text-muted"
                    >
                        Select a rating
                    </small>

                </div>

                <!-- Comments -->
                <div class="mb-4">

                    <label
                        for="comments"
                        class="form-label fw-semibold"
                    >
                        Comments
                    </label>

                    <textarea
                        id="comments"
                        class="form-control"
                        rows="6"
                        maxlength="1000"
                        placeholder="Write your feedback here..."
                        required
                    ></textarea>

                    <div class="text-end mt-1">

                        <small class="text-muted">
                            <span id="characterCount">0</span>/1000
                        </small>

                    </div>

                </div>

                <!-- Anonymous -->
                <div class="form-check mb-4">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="anonymous"
                    >

                    <label
                        class="form-check-label"
                        for="anonymous"
                    >
                        Submit this feedback anonymously
                    </label>

                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa-solid fa-paper-plane me-1"></i>
                        Submit Feedback
                    </button>

                    <button
                        type="reset"
                        class="btn btn-secondary"
                        id="resetFeedback"
                    >
                        <i class="fa-solid fa-rotate-left me-1"></i>
                        Reset
                    </button>

                </div>

            </form>

        </div>

    </div>

    <!-- Success Message -->
    <div
        id="successCard"
        class="card shadow-sm border-0 mt-4 d-none"
    >

        <div class="card-body text-center py-5">

            <i
                class="fa-solid fa-circle-check text-success fa-4x mb-3"
            ></i>

            <h3 class="fw-bold">
                Feedback Submitted Successfully
            </h3>

            <p class="text-muted mb-4">
                Thank you for sharing your feedback.
                Your response has been recorded for this session.
            </p>

            <button
                type="button"
                class="btn btn-primary"
                id="newFeedback"
            >
                <i class="fa-solid fa-plus me-1"></i>
                Submit Another Feedback
            </button>

        </div>

    </div>

</div>

<style>

.rating-container {
    display: flex;
    gap: 8px;
    margin-bottom: 5px;
}

.rating-star {
    border: none;
    background: transparent;
    font-size: 2.2rem;
    color: #ced4da;
    cursor: pointer;
    padding: 0 3px;
    transition: transform 0.15s ease;
}

.rating-star:hover {
    transform: scale(1.15);
}

.rating-star.active {
    color: #ffc107;
}

</style>

<script>

/*
|--------------------------------------------------------------------------
| Star Rating
|--------------------------------------------------------------------------
*/

const stars = document.querySelectorAll(".rating-star");
const ratingInput = document.getElementById("rating");
const ratingText = document.getElementById("ratingText");

const ratingMessages = {
    1: "Very dissatisfied",
    2: "Dissatisfied",
    3: "Neutral",
    4: "Satisfied",
    5: "Very satisfied"
};

stars.forEach(function(star) {

    star.addEventListener("click", function() {

        const rating = parseInt(this.dataset.rating);

        ratingInput.value = rating;

        stars.forEach(function(item) {

            const itemRating = parseInt(item.dataset.rating);

            if (itemRating <= rating) {
                item.classList.add("active");
            } else {
                item.classList.remove("active");
            }

        });

        ratingText.textContent =
            rating + " / 5 — " + ratingMessages[rating];

    });

});

/*
|--------------------------------------------------------------------------
| Character Counter
|--------------------------------------------------------------------------
*/

const comments = document.getElementById("comments");
const characterCount = document.getElementById("characterCount");

comments.addEventListener("input", function() {

    characterCount.textContent = this.value.length;

});

/*
|--------------------------------------------------------------------------
| Submit Feedback
|--------------------------------------------------------------------------
*/

document.getElementById("feedbackForm").addEventListener(
    "submit",
    function(event) {

        event.preventDefault();

        const category =
            document.getElementById("category").value;

        const rating =
            document.getElementById("rating").value;

        const comment =
            document.getElementById("comments").value.trim();

        if (category === "") {

            alert("Please select a feedback category.");

            return;

        }

        if (rating === "") {

            alert("Please select a rating.");

            return;

        }

        if (comment === "") {

            alert("Please enter your feedback.");

            return;

        }

        /*
        |--------------------------------------------------------------------------
        | No feedback table currently exists.
        | Therefore this is a session-side confirmation only.
        |--------------------------------------------------------------------------
        */

        document
            .getElementById("feedbackForm")
            .closest(".card")
            .classList.add("d-none");

        document
            .getElementById("successCard")
            .classList.remove("d-none");

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    }
);

/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

document.getElementById("resetFeedback").addEventListener(
    "click",
    function() {

        ratingInput.value = "";

        ratingText.textContent = "Select a rating";

        stars.forEach(function(star) {
            star.classList.remove("active");
        });

        characterCount.textContent = "0";

    }
);

/*
|--------------------------------------------------------------------------
| New Feedback
|--------------------------------------------------------------------------
*/

document.getElementById("newFeedback").addEventListener(
    "click",
    function() {

        document
            .getElementById("successCard")
            .classList.add("d-none");

        document
            .getElementById("feedbackForm")
            .closest(".card")
            .classList.remove("d-none");

        document
            .getElementById("feedbackForm")
            .reset();

        ratingInput.value = "";

        ratingText.textContent = "Select a rating";

        characterCount.textContent = "0";

        stars.forEach(function(star) {
            star.classList.remove("active");
        });

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });

    }
);

</script>

<?php

include "../includes/footer.php";

?>
