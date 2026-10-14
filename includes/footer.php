</main>
<footer class="foot"><b>LVHS Online Enrollment</b><span>Enrollment made simpler for students, parents, and the registrar.</span><a href="ending.html">Finish &amp; say goodbye</a><small>&copy; <?= date('Y') ?> LVHS</small></footer>
<script src="assets/app.js"></script>
<script>
(function(){
  var ax=document.getElementById('ax'); if(!ax) return;
  var btn=document.getElementById('axBtn'), modal=document.getElementById('axModal');
  function setOpen(o){ax.classList.toggle('open',o);btn.setAttribute('aria-expanded',o)}
  function askLogout(){setOpen(false);modal.hidden=false;document.querySelector('.ax-go').focus()}
  btn.addEventListener('click',function(e){e.stopPropagation();setOpen(!ax.classList.contains('open'))});
  document.addEventListener('click',function(e){if(!ax.contains(e.target))setOpen(false)});
  document.getElementById('axLogout').addEventListener('click',function(e){e.preventDefault();askLogout()});
  document.getElementById('axStay').addEventListener('click',function(){modal.hidden=true});
  modal.addEventListener('click',function(e){if(e.target===modal)modal.hidden=true});
  document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){setOpen(false);modal.hidden=true;return}
    if(!ax.classList.contains('open'))return;
    var k=e.key.toLowerCase();
    if(k==='s')location.href='switch_account.php';
    if(k==='l')askLogout();
  });
})();
</script>
</body></html>