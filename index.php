<?php require_once __DIR__.'/config.php'; $acct = account_info($pdo); $role = $acct['role'] ?? null; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>LVHS Online Enrollment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=Sora:wght@500;700;800&family=Source+Sans+3:wght@400;600&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="assets/style.css" />
    <link rel="stylesheet" href="assets/hero.css" />

    <!-- ===================== COOL PHOTO BACKGROUND (CSS) ===================== -->
    <style>
      /* >>> YOUR PHOTO: put it at assets/bg.jpg (or change the path below) <<< */
      :root { --bg-photo: url('assets/bg.jpg'); }

      html { background: #05060f; }
      body { background: transparent !important; }

      .bg { position: fixed; inset: 0; z-index: -1; overflow: hidden; pointer-events: none; }
      .bg > * { position: absolute; }

      /* Photo with slow Ken Burns zoom + mouse parallax */
      .bg-photo {
        inset: -5%;
        transform: translate3d(var(--px, 0px), var(--py, 0px), 0);
        transition: transform .4s ease-out;
      }
      .bg-photo::before {
        content: ""; position: absolute; inset: 0;
        background: var(--bg-photo) center / cover no-repeat;
        filter: saturate(1.25) contrast(1.08);
        animation: kenburns 28s ease-in-out infinite alternate;
      }
      @keyframes kenburns {
        from { transform: scale(1) rotate(0deg); }
        to   { transform: scale(1.16) rotate(.8deg); }
      }

      /* Slowly shifting color wash */
      .bg-tint {
        inset: 0;
        background: linear-gradient(120deg, #6a2cff, #00d4ff, #ff3d9a, #ffb400, #6a2cff);
        background-size: 400% 400%;
        mix-blend-mode: soft-light;
        opacity: .55;
        animation: tintShift 22s ease infinite;
      }
      @keyframes tintShift {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
      }

      /* Glowing aurora orbs */
      .bg-orb {
        border-radius: 50%;
        filter: blur(90px);
        mix-blend-mode: screen;
        opacity: .55;
        will-change: transform;
      }
      .bg-orb.o1 { width: 46vmax; height: 46vmax; left: -12vmax; top: -14vmax;
        background: radial-gradient(circle, #7c3aed, transparent 65%); animation: drift1 24s ease-in-out infinite alternate; }
      .bg-orb.o2 { width: 40vmax; height: 40vmax; right: -10vmax; top: 18%;
        background: radial-gradient(circle, #06b6d4, transparent 65%); animation: drift2 30s ease-in-out infinite alternate; }
      .bg-orb.o3 { width: 36vmax; height: 36vmax; left: 25%; bottom: -16vmax;
        background: radial-gradient(circle, #ec4899, transparent 65%); animation: drift3 27s ease-in-out infinite alternate; }
      @keyframes drift1 { to { transform: translate(18vw, 14vh) scale(1.25); } }
      @keyframes drift2 { to { transform: translate(-20vw, 10vh) scale(.85); } }
      @keyframes drift3 { to { transform: translate(14vw, -16vh) scale(1.2); } }

      /* Faint tech grid */
      .bg-grid {
        inset: 0;
        background-image:
          linear-gradient(rgba(255,255,255,.07) 1px, transparent 1px),
          linear-gradient(90deg, rgba(255,255,255,.07) 1px, transparent 1px);
        background-size: 64px 64px;
        -webkit-mask-image: radial-gradient(ellipse at center, #000 20%, transparent 75%);
                mask-image: radial-gradient(ellipse at center, #000 20%, transparent 75%);
        animation: gridMove 20s linear infinite;
      }
      @keyframes gridMove { to { background-position: 64px 64px, 64px 64px; } }

      /* Mouse spotlight */
      .bg-spot {
        inset: 0;
        background: radial-gradient(500px circle at var(--sx, 50%) var(--sy, 40%),
                    rgba(255,255,255,.18), transparent 60%);
        mix-blend-mode: overlay;
      }

      /* Floating particles (created by JS) */
      .bg-particles { inset: 0; }
      .bg-particles i {
        position: absolute; bottom: -10px;
        width: var(--s); height: var(--s);
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 0 12px 3px rgba(255,255,255,.7);
        opacity: 0;
        animation: rise var(--d) linear var(--dl) infinite;
      }
      @keyframes rise {
        0%   { transform: translateY(0) translateX(0); opacity: 0; }
        10%  { opacity: .9; }
        90%  { opacity: .9; }
        100% { transform: translateY(-110vh) translateX(var(--x)); opacity: 0; }
      }

      /* Film grain */
      .bg-grain {
        inset: -50%;
        opacity: .12;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
        animation: grain 1s steps(6) infinite;
      }
      @keyframes grain {
        0% { transform: translate(0,0); } 20% { transform: translate(-3%,2%); }
        40% { transform: translate(2%,-3%); } 60% { transform: translate(-2%,-1%); }
        80% { transform: translate(3%,3%); } 100% { transform: translate(0,0); }
      }

      /* Vignette keeps text readable. Lower the .35/.55 numbers for a brighter photo. */
      .bg-vignette {
        inset: 0;
        background:
          radial-gradient(ellipse at center, transparent 35%, rgba(0,0,0,.65) 100%),
          linear-gradient(to bottom, rgba(5,6,15,.35), rgba(5,6,15,.55));
      }

      @media (prefers-reduced-motion: reduce) {
        .bg *, .bg *::before { animation: none !important; }
      }
      @media (max-width: 700px) {
        .bg-orb { filter: blur(60px); }
        .bg-grid, .bg-grain { display: none; }
      }
      /* Keep the hero headline always visible */
.hero,
.hero > div,
.hero .wlc,
.hero .wlc-top,
.hero .wlc-big,
.hero .wlc-big span,
.hero .wlc-type,
.hero .wlc-caret,
.hero p,
.hero .row,
.hero .btn {
  animation: none !important;
  opacity: 1 !important;
  transform: none !important;
  visibility: visible !important;
}
    </style>
  </head>
  <body>
    <!-- ===================== COOL PHOTO BACKGROUND (HTML) ===================== -->
    <div class="bg" aria-hidden="true">
      <div class="bg-photo"></div>
      <div class="bg-tint"></div>
      <div class="bg-orb o1"></div>
      <div class="bg-orb o2"></div>
      <div class="bg-orb o3"></div>
      <div class="bg-grid"></div>
      <div class="bg-spot"></div>
      <div class="bg-particles" id="bgParticles"></div>
      <div class="bg-grain"></div>
      <div class="bg-vignette"></div>
    </div>

    <header class="nav">
      <a class="brand" href="index.php"
        ><b>LVHS</b><span>Online Enrollment</span></a
      >
      <nav>
        <a href="index.php">Home</a><a href="map.php">Map</a
        ><a href="#about">About</a><a href="#portals">Portals</a
        ><a href="#contact">Contact</a>
        <?php if ($role === 'student'): ?>
          <a href="temporary_id.php">Temporary ID</a><a class="pill" href="dashboard.php">Dashboard</a>
        <?php elseif ($role === 'registrar'): ?>
          <a class="pill" href="registrar.php">Registrar</a>
        <?php else: ?>
          <a href="registrar_login.php">Registrar</a><a class="pill" href="login.php" data-auth>Student sign in</a>
        <?php endif; ?>

        <?php if ($role): $ini = strtoupper(mb_substr(trim($acct['name']), 0, 1)); ?>
          <div class="ax" id="ax">
            <button class="ax-btn" id="axBtn" type="button" aria-haspopup="menu" aria-expanded="false">
              <span class="ax-ring"><span class="ax-av"><?= e($ini) ?></span></span>
              <span class="ax-who"><b><?= e($acct['name']) ?></b><small><?= e(ucfirst($role)) ?></small></span>
              <svg class="ax-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            </button>

            <div class="ax-menu" id="axMenu" role="menu">
              <div class="ax-card">
                <span class="ax-av big"><?= e($ini) ?></span>
                <div><b><?= e($acct['name']) ?></b><small><?= e($acct['email']) ?></small>
                  <em class="ax-badge"><?= e(ucfirst($role)) ?></em></div>
              </div>

              <a class="ax-item" role="menuitem" href="switch_account.php" style="--i:1">
                <i>🔄</i><span>Switch account<small>Sign in as someone else</small></span><kbd>S</kbd></a>

              <div class="ax-sep"></div>
              <a class="ax-item danger" role="menuitem" href="logout.php" id="axLogout" style="--i:2">
                <i>⏻</i><span>Log out<small>End this session</small></span><kbd>L</kbd></a>
            </div>
          </div>
        <?php endif; ?>
      </nav>
    </header>

    <?php if ($role): ?>
      <div class="ax-modal" id="axModal" hidden>
        <div class="ax-dialog" role="dialog" aria-modal="true" aria-labelledby="axT">
          <div class="ax-wave">👋</div>
          <h3 id="axT">Leaving so soon, <?= e(explode(' ', trim($acct['name']))[0]) ?>?</h3>
          <p>You'll need to sign in again to keep working on your enrollment.</p>
          <div class="ax-actions">
            <button type="button" id="axStay">Stay signed in</button>
            <a href="logout.php" class="ax-go">Log out</a>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <main>
      <div class="toast" id="sentToast" role="status" hidden></div>

      <div class="hero">
        <div>
          <h1 class="wlc" aria-label="Welcome to LVHS">
            <span class="wlc-top" aria-hidden="true">Welcome to</span>
            <span class="wlc-big" aria-hidden="true"
              ><span style="--i: 0">L</span><span style="--i: 1">V</span
              ><span style="--i: 2">H</span><span style="--i: 3">S</span></span
            >
          </h1>
          <div class="wlc-type" aria-hidden="true">
            <span class="star">&#10022;</span><span id="wlcTyped"></span
            ><i class="wlc-caret"></i>
          </div>
          <p>
            Submit your requirements, track your application, get your temporary
            ID and find your way around campus, all in one place.
          </p>
          <div class="row">
            <?php if ($role): ?>
              <a class="btn" href="<?= $role === 'registrar' ? 'registrar.php' : 'dashboard.php' ?>">Go to my dashboard</a>
            <?php else: ?>
              <a class="btn" href="register.php">Student sign up</a>
            <?php endif; ?>
            <a class="btn ghost" href="#about">Learn more</a>
          </div>
        </div>
        <div class="tilt">
          <div class="hero-card">
            <h3>Enroll online</h3>
            <p class="card-note"></p>
            <div class="steps">
              <span>1</span><span>2</span><span>3</span><span>4</span>
            </div>
          </div>
        </div>
      </div>

      <section id="about">
        <h2>Welcome to LVHS Online Enrollment</h2>
        <p class="lead">
          Students finish enrollment digitally. The registrar gets one place to
          review pending enrollees and the requirements they passed.
        </p>
        <div class="grid">
          <div class="card">
            <i class="tag"></i>
            <h3>Easy enrollment</h3>
            <p>Complete one form, step by step.</p>
          </div>
          <div class="card">
            <i class="tag"></i>
            <h3>Temporary ID</h3>
            <p>
              Your details are filled in automatically. Add your photo and save
              your ID as a picture.
            </p>
          </div>
          <div class="card">
            <i class="tag"></i>
            <h3>Campus map</h3>
            <p>
              Pick a number and the map zooms right to that building or room.
            </p>
            <a href="map.php">Open the map</a>
          </div>
        </div>
      </section>

      <section id="portals">
        <h2>Two separate portals</h2>
        <p class="lead">
          Students and the registrar each have their own sign in and sign up.
        </p>
        <div class="grid">
          <div class="card">
            <h3>Student portal</h3>
            <p>
              Account, enrollment form, requirements, temporary ID and status.
            </p>
            <div class="row">
              <a class="btn small" href="login.php">Sign in</a
              ><a class="btn small ghost" href="register.php">Sign up</a>
            </div>
          </div>
          <div class="card">
            <h3>Registrar portal</h3>
            <p>
              Review pending enrollees, see every requirement they passed, and
              update statuses.
            </p>
            <div class="row">
              <a class="btn small" href="registrar_login.php">Sign in</a
              ><a class="btn small ghost" href="registrar_register.php"
                >Sign up</a
              >
            </div>
          </div>
        </div>
      </section>

      <section id="contact">
        <h2>Contact LVHS</h2>
        <br />
        <form class="panel" method="post" action="contact.php" data-busy>
          <label for="name">Name</label><input id="name" name="name" required />
          <label for="email">Email</label
          ><input id="email" name="email" type="email" required />
          <label for="message">Message</label
          ><textarea id="message" name="message" required></textarea><br />
          <button class="btn" type="submit">Send message</button>
        </form>
      </section>
    </main>
    <footer class="foot">
      <b>LVHS Online Enrollment</b
      ><span
        >Enrollment made simpler for students, parents, and the registrar.</span
      ><a href="ending.html">Finish &amp; say goodbye</a
      ><small>&copy; 2026 LVHS</small>
    </footer>
    <script src="assets/app.js"></script>

    <script>
(function () {
  var btn = document.getElementById('axBtn');
  var menu = document.getElementById('axMenu');
  var box = document.getElementById('ax');
  if (!btn || !menu) return;

  function setOpen(open) {
    box.classList.toggle('open', open);
    menu.classList.toggle('open', open);
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  btn.addEventListener('click', function (e) {
    e.stopPropagation();
    setOpen(!menu.classList.contains('open'));
  });
  document.addEventListener('click', function (e) {
    if (!box.contains(e.target)) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') setOpen(false);
  });

  // "Leaving so soon" popup
  var modal = document.getElementById('axModal');
  var logout = document.getElementById('axLogout');
  var stay = document.getElementById('axStay');
  if (modal && logout) {
    logout.addEventListener('click', function (e) {
      e.preventDefault();
      modal.hidden = false;
    });
  }
  if (modal && stay) {
    stay.addEventListener('click', function () { modal.hidden = true; });
  }
})();
</script>

    <script>
      /* hero headline: mouse spotlight + typewriter */
      (function () {
        var h = document.querySelector(".wlc");
        var out = document.getElementById("wlcTyped");
        if (!h || !out) return;

        h.parentElement.addEventListener("pointermove", function (e) {
          var r = h.getBoundingClientRect();
          h.style.setProperty("--mx", e.clientX - r.left + "px");
          h.style.setProperty("--my", e.clientY - r.top + "px");
        });

        var words = [
          "Enroll online.",
          "Track your status.",
          "Get your temporary ID.",
          "Find any building.",
        ];
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
          out.textContent = words[0];
          return;
        }
        var w = 0, c = 0, del = false;
        (function tick() {
          var word = words[w];
          out.textContent = word.slice(0, c);
          var wait = del ? 35 : 75;
          if (!del && c === word.length) { del = true; wait = 1500; }
          else if (del && c === 0) { del = false; w = (w + 1) % words.length; wait = 350; }
          else c += del ? -1 : 1;
          setTimeout(tick, wait);
        })();
      })();
    </script>

    <!-- ===================== COOL PHOTO BACKGROUND (JS) ===================== -->
    <script>
(function () {
  var bg = document.querySelector('.bg');
  if (!bg) return;

  
  });

  // floating particles
  var box = document.getElementById('bgParticles');
  var n = innerWidth < 700 ? 18 : 40;
  for (var i = 0; i < n; i++) {
    var p = document.createElement('i');
    p.style.left = Math.random() * 100 + '%';
    p.style.setProperty('--s', (Math.random() * 4 + 2) + 'px');
    p.style.setProperty('--d', (Math.random() * 14 + 12) + 's');
    p.style.setProperty('--dl', (-Math.random() * 20) + 's');
    p.style.setProperty('--x', (Math.random() * 120 - 60) + 'px');
    box.appendChild(p);
  }
})();
</script>
  </body>
</html>