function updateID() {
  const studentName = document.getElementById("studentName").value.trim();

  const lrn = document.getElementById("lrn").value.trim();

  const yearLevel = document.getElementById("yearLevel").value;

  const emergencyName = document.getElementById("emergencyName").value.trim();

  const emergencyAddress = document
    .getElementById("emergencyAddress")
    .value.trim();

  const emergencyContact = document
    .getElementById("emergencyContact")
    .value.trim();

  // FRONT

  document.getElementById("displayStudentName").textContent = studentName;

  document.getElementById("displayLRN").textContent = lrn;

  document.getElementById("displayYearLevel").textContent = yearLevel;

  // BACK

  document.getElementById("displayEmergencyName").textContent = emergencyName;

  document.getElementById("displayEmergencyAddress").textContent =
    emergencyAddress;

  document.getElementById("displayEmergencyContact").textContent =
    emergencyContact;
}

function showStudentPhoto(file) {
  if (!file) {
    return;
  }

  if (!file.type.startsWith("image/")) {
    alert("Please select an image file.");
    return;
  }

  const reader = new FileReader();

  reader.onload = function (event) {
    const photo = document.getElementById("studentPhotoPreview");

    const photoArea = document.querySelector(".photo-upload-area");

    photo.src = event.target.result;

    photoArea.classList.add("has-photo");
  };

  reader.readAsDataURL(file);
}

const studentPhotoForm = document.getElementById("studentPhotoForm");

if (studentPhotoForm) {
  studentPhotoForm.addEventListener("change", function () {
    const file = this.files[0];

    showStudentPhoto(file);
  });
}

const studentPhoto = document.getElementById("studentPhoto");

if (studentPhoto) {
  studentPhoto.addEventListener("change", function () {
    const file = this.files[0];

    if (!file) {
      return;
    }

    showStudentPhoto(file);

    // Copy selected file to the other input

    const formInput = document.getElementById("studentPhotoForm");

    try {
      const dataTransfer = new DataTransfer();

      dataTransfer.items.add(file);

      formInput.files = dataTransfer.files;
    } catch (error) {
      console.log("File synchronization not available.");
    }
  });
}

function waitForImages(element) {
  const images = element.querySelectorAll("img");

  const promises = [];

  images.forEach(function (img) {
    if (!img.complete) {
      promises.push(
        new Promise(function (resolve) {
          img.onload = resolve;

          img.onerror = resolve;
        }),
      );
    }
  });

  return Promise.all(promises);
}

async function saveIDAsPicture() {
  // Update information first

  updateID();

  const studentName = document.getElementById("studentName").value.trim();

  // Check name

  if (studentName === "") {
    alert("Please enter your student name first.");

    return;
  }

  const frontID = document.getElementById("frontID");

  const backID = document.getElementById("backID");

  if (!frontID || !backID) {
    alert("The ID layout could not be found.");

    return;
  }

  try {
    await waitForImages(frontID);

    await waitForImages(backID);

    const frontCanvas = await html2canvas(frontID, {
      scale: 2,
      useCORS: true,
      backgroundColor: "#ffffff",
    });

    const backCanvas = await html2canvas(backID, {
      scale: 2,
      useCORS: true,
      backgroundColor: "#ffffff",
    });

    const gap = 30;

    const width = Math.max(frontCanvas.width, backCanvas.width);

    const height = frontCanvas.height + gap + backCanvas.height;

    const combinedCanvas = document.createElement("canvas");

    combinedCanvas.width = width;

    combinedCanvas.height = height;

    const ctx = combinedCanvas.getContext("2d");

    // White background

    ctx.fillStyle = "#ffffff";

    ctx.fillRect(0, 0, width, height);

    const frontX = (width - frontCanvas.width) / 2;

    ctx.drawImage(frontCanvas, frontX, 0);

    const backX = (width - backCanvas.width) / 2;

    ctx.drawImage(backCanvas, backX, frontCanvas.height + gap);

    const cleanName = studentName.replace(/[^\w\s-]/g, "").replace(/\s+/g, "_");

    const fileName = cleanName + "_Temporary_ID.png";

    combinedCanvas.toBlob(function (blob) {
      if (!blob) {
        alert("Unable to create the ID image.");

        return;
      }

      const url = URL.createObjectURL(blob);

      const link = document.createElement("a");

      link.href = url;

      link.download = fileName;

      document.body.appendChild(link);

      link.click();

      document.body.removeChild(link);

      // Release memory

      setTimeout(function () {
        URL.revokeObjectURL(url);
      }, 1000);

      alert("Temporary ID downloaded successfully!");
    }, "image/png");
  } catch (error) {
    console.error("DOWNLOAD ERROR:", error);

    alert(
      "Download failed. Please make sure front.jpg and back.jpg are in the same folder as your HTML file.",
    );
  }
}
