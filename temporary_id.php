<?php
// config.php starts the session (starting it twice raises a PHP notice), then the usual student-only access check.
require_once __DIR__ . '/config.php';
require_login('student');

$stmt = $pdo->prepare('SELECT * FROM students WHERE user_id = ?');
$stmt->execute([$_SESSION['uid']]);
$student = $stmt->fetch();

// No enrollment form yet -> send them to fill it in first
if (!$student) {
    header('Location: enroll.php');
    exit;
}

$fullName = trim(
    ($student['first_name'] ?? '') . ' ' .
    ($student['middle_name'] ?? '') . ' ' .
    ($student['last_name'] ?? '')
);
$fullName = preg_replace('/\s+/', ' ', $fullName);

$lrn = $student['lrn'] ?? '';

// Works whether grade_level is saved as "11" or "Grade 11"
$gradeNum = preg_replace('/\D/', '', $student['grade_level'] ?? '');
$gradeText = $gradeNum !== '' ? 'GRADE ' . $gradeNum : '';

// Shared site header: background, navigation bar, account menu, logout popup
$title = 'Temporary ID';
$active = 'temporary_id';
$css = ['style2.css'];   // ID-card positioning only (scoped to .id-page)
require __DIR__ . '/includes/header.php';
?>

<div class="id-page">

    <div class="id-container">

        <h1 style="font-size:2.6rem">
            Temporary Student ID
        </h1>

        <p class="lead">
            Your enrollment info is filled in below — add your emergency contact details and upload your picture to
            create your temporary ID.
        </p>

        <div class="id-preview">


            <div class="id-card" id="frontID">

                <!-- FRONT ID BACKGROUND -->

                <img src="./front.jpg" class="id-background" alt="Temporary ID Front">

                <label for="studentPhoto" class="photo-upload-area" title="Click to upload your picture">

                    <img id="studentPhotoPreview" class="student-photo" src="" alt="Student Photo">

                </label>


                <input type="file" id="studentPhoto" accept="image/*" hidden>

                <div id="displayStudentName" class="id-input-value student-name-value">
                    <?php echo htmlspecialchars($fullName); ?>
                </div>


                <div class="id-original-label student-name-label">
                    STUDENT NAME
                </div>


                <div id="displayLRN" class="id-input-value lrn-value">
                    <?php echo htmlspecialchars($lrn); ?>
                </div>


                <div class="id-original-label lrn-label">
                    LRN
                </div>


                <div id="displayYearLevel" class="id-input-value year-level-value">
                    <?php echo htmlspecialchars($gradeText); ?>
                </div>


                <!-- ORIGINAL YEAR LEVEL LABEL -->

                <div class="id-original-label year-level-label">
                    YEAR LEVEL
                </div>

            </div>

            <div class="id-card" id="backID">

                <!-- BACK ID BACKGROUND -->

                <img src="back.jpg" class="id-background" alt="Temporary ID Back">

                <div id="displayEmergencyName" class="back-input-value emergency-name-value">
                </div>


                <div class="back-original-label emergency-name-label">
                    NAME:
                </div>


                <div id="displayEmergencyAddress" class="back-input-value emergency-address-value">
                </div>

                <div class="back-original-label emergency-address-label">
                    ADDRESS:
                </div>


                <div id="displayEmergencyContact" class="back-input-value emergency-contact-value">
                </div>


                <div class="back-original-label emergency-contact-label">
                    CONTACT #:
                </div>

            </div>

        </div>


        <section class="panel information-section">

            <h2 class="sec first">
                Confirm Your Information
            </h2>

            <p class="form-description">
                Your name, LRN, and year level were pulled from your enrollment record. Fill in your emergency contact
                and upload your picture, then click Update ID.
            </p>


            <div class="form-grid">


                <div class="form-group">

                    <label for="studentName">
                        Student Name
                    </label>

                    <input type="text" id="studentName" value="<?php echo htmlspecialchars($fullName); ?>">

                </div>

                <div class="form-group">

                    <label for="lrn">
                        LRN
                    </label>

                    <input type="text" id="lrn" value="<?php echo htmlspecialchars($lrn); ?>" maxlength="20" readonly>

                </div>


                <div class="form-group">

                    <label for="yearLevel">
                        Year Level
                    </label>

                    <select id="yearLevel">

                        <option value="">
                            Select Year Level
                        </option>

                        <option value="GRADE 7" <?php echo $gradeNum === '7' ? 'selected' : ''; ?>>
                            Grade 7
                        </option>

                        <option value="GRADE 8" <?php echo $gradeNum === '8' ? 'selected' : ''; ?>>
                            Grade 8
                        </option>

                        <option value="GRADE 9" <?php echo $gradeNum === '9' ? 'selected' : ''; ?>>
                            Grade 9
                        </option>

                        <option value="GRADE 10" <?php echo $gradeNum === '10' ? 'selected' : ''; ?>>
                            Grade 10
                        </option>

                        <option value="GRADE 11" <?php echo $gradeNum === '11' ? 'selected' : ''; ?>>
                            Grade 11
                        </option>

                        <option value="GRADE 12" <?php echo $gradeNum === '12' ? 'selected' : ''; ?>>
                            Grade 12
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="emergencyName">
                        Emergency Contact Name
                    </label>

                    <input type="text" id="emergencyName" placeholder="Enter emergency contact name">

                </div>

                <div class="form-group">

                    <label for="emergencyAddress">
                        Emergency Contact Address
                    </label>

                    <input type="text" id="emergencyAddress" placeholder="Enter emergency contact address">

                </div>



                <div class="form-group">

                    <label for="emergencyContact">
                        Emergency Contact Number
                    </label>

                    <input type="text" id="emergencyContact" placeholder="Enter contact number">

                </div>


                <div class="form-group photo-form-group">

                    <label for="studentPhotoForm">
                        Student Picture
                    </label>

                    <input type="file" id="studentPhotoForm" accept="image/*">

                    <small>
                        You can also click the photo area on the ID above.
                    </small>

                </div>

            </div>


            <div class="button-container">

                <button type="button" class="btn update-button" onclick="updateID()">

                    Update ID

                </button>


                <button type="button" class="btn ghost save-button" onclick="saveIDAsPicture()">

                    Save Picture

                </button>

            </div>

        </section>

    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="script2.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>