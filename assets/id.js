// Temporary ID: live preview, photo upload, save as picture
const $ = (id) => document.getElementById(id);
const bind = (input, out, fmt = (v) => v) => {
  const f = () => ($(out).textContent = fmt($(input).value));
  $(input).addEventListener("input", f);
  $(input).addEventListener("change", f);
};
bind("studentName", "displayStudentName");
bind("lrn", "displayLRN");
bind("yearLevel", "displayYearLevel");
bind("emergencyName", "displayEmergencyName");
bind("emergencyAddress", "displayEmergencyAddress");
bind("emergencyContact", "displayEmergencyContact");

$("studentPhoto").addEventListener("change", async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  const img = $("studentPhotoPreview"),
    msg = $("photoMsg"),
    hint = $("photoHint");
  img.src = URL.createObjectURL(file);
  if (hint) hint.hidden = true; // show it on the ID straight away
  msg.textContent = "Saving picture...";
  const fd = new FormData();
  fd.append("photo", file);
  fd.append("csrf", $("csrfToken").value);
  try {
    const res = await fetch("temporary_id.php", { method: "POST", body: fd });
    const j = await res.json();
    msg.textContent = j.ok
      ? "Picture saved. It will be here next time you open your ID."
      : j.error || "Could not save the picture.";
  } catch (err) {
    msg.textContent = "Could not save the picture. Check your connection.";
  }
});

async function savePicture(cardId, fileName) {
  if (typeof html2canvas === "undefined") {
    alert(
      "The picture tool could not load. Check your internet connection and try again.",
    );
    return;
  }
  const canvas = await html2canvas($(cardId), {
    scale: 3,
    backgroundColor: null,
    useCORS: true,
    onclone: (doc) =>
      doc.querySelectorAll("*").forEach((n) => {
        n.style.animation = "none";
        n.style.transition = "none";
      }),
  });
  const a = document.createElement("a");
  a.download = fileName;
  a.href = canvas.toDataURL("image/png");
  a.click();
}
$("saveFront").onclick = () =>
  savePicture("frontID", "LVHS-Temporary-ID-front.png");
$("saveBack").onclick = () =>
  savePicture("backID", "LVHS-Temporary-ID-back.png");
