const dots = [...document.querySelectorAll(".steps span")];
if (dots.length) {
  let i = 0;
  const t = document.querySelector(".card-note");
  const tick = () => {
    dots.forEach((d, k) => d.classList.toggle("on", k === i));
    if (t)
      t.textContent = [
        "Create your account",
        "Fill in the enrollment form",
        "Upload your requirements",
        "Track your status",
      ][i];
    i = (i + 1) % dots.length;
  };
  tick();
  setInterval(tick, 1800);
}
const c = document.querySelector(".hero-card");
if (c && !matchMedia("(prefers-reduced-motion: reduce)").matches) {
  const p = c.parentElement;
  p.addEventListener("mousemove", (e) => {
    const r = c.getBoundingClientRect();
    c.style.transform = `rotateY(${((e.clientX - r.left) / r.width - 0.5) * 10}deg) rotateX(${-((e.clientY - r.top) / r.height - 0.5) * 10}deg)`;
  });
  p.addEventListener("mouseleave", () => (c.style.transform = ""));
}
document.querySelectorAll("form[data-busy]").forEach((f) =>
  f.addEventListener("submit", () => {
    const b = f.querySelector("button[type=submit]");
    if (b) setTimeout(() => b.classList.add("busy"), 10);
  }),
);

// index.html: show a message after the contact form is sent (contact.php redirects back with ?sent=)
const sent = new URLSearchParams(location.search).get("sent"),
  sentToast = document.getElementById("sentToast");
if (sentToast && sent !== null) {
  sentToast.hidden = false;
  sentToast.classList.toggle("err", sent !== "1");
  sentToast.textContent =
    sent === "1"
      ? "Message sent. We will reply by email."
      : "Please fill in your name, a valid email, and a message.";
}

// Static pages (index.html, ending.html): swap "Student sign in" for the signed-in account.
// The account comes from auth_status.php (PHP session). If PHP is not running, the sign-in link simply stays.
(function () {
  const slot = document.querySelector("nav a[data-auth]");
  if (!slot) return;
  const mk = (tag, cls, href, text) => {
    const x = document.createElement(tag);
    if (cls) x.className = cls;
    if (href) x.href = href;
    x.textContent = text;
    return x;
  };
  const original = slot.cloneNode(true);
  let added = [];
  function render(a) {
    added.forEach((n) => n.remove());
    added = [];
    const reg = document.querySelector('nav a[href="registrar_login.php"]');
    if (!a || !a.loggedIn) {
      slot.hidden = false;
      if (reg) reg.hidden = false;
      return;
    }
    slot.hidden = true;
    if (reg) reg.hidden = true;
    const box = document.createElement("span");
    box.className = "acct";
    const id = document.createElement("span");
    id.className = "acct-id";
    id.append(mk("b", 0, 0, a.name), mk("small", 0, 0, a.email));
    box.append(
      id,
      mk("a", 0, "switch_account.php", "Switch account"),
      mk("a", 0, "logout.php", "Log out"),
    );
    box.children[1].title = "Log in to another account";
    const home = mk(
      "a",
      "pill",
      a.role === "registrar" ? "registrar.php" : "dashboard.php",
      a.role === "registrar" ? "Registrar" : "Dashboard",
    );
    slot.after(home, box);
    added = [home, box];
  }
  function load() {
    fetch("auth_status.php", { cache: "no-store", credentials: "same-origin" })
      .then((r) => (r.ok ? r.json() : null))
      .then(render)
      .catch(() => {});
  }
  load();
  addEventListener("pageshow", (e) => {
    if (e.persisted) load();
  }); // back button: re-check instead of trusting the cached page
})();

// index.html: confirm after logout.php redirects here with ?out=1
if (sentToast && new URLSearchParams(location.search).get("out") === "1") {
  sentToast.hidden = false;
  sentToast.classList.remove("err");
  sentToast.textContent = "You have been logged out.";
  history.replaceState(null, "", location.pathname + location.hash);
}
