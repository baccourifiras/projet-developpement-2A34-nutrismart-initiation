<?php
/**
 * Layout : Front office - FOOTER
 * /views/layouts/front_footer.php
 */
$baseUrl = '/' . basename(BASE_PATH);
?>
  <footer class="site-footer">
    <p>© <?= date('Y') ?> <strong>NutriSmart</strong> &middot; Eat Smart Live Smart</p>
  </footer>

  <?php include BASE_PATH . '/views/components/chatbot_widget.php'; ?>

  <script>
    /* Navbar : effet scroll + lien actif (script existant) */
    (function () {
      var navbar = document.getElementById('navbar');
      window.addEventListener('scroll', function () {
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
      });
      var current = window.location.pathname.split('/').pop() || 'accueil.php';
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        if (link.getAttribute('href').endsWith(current)) link.classList.add('active');
      });
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        link.addEventListener('click', function () {
          this.style.transform = 'scale(0.93)';
          var self = this;
          setTimeout(function () { self.style.transform = ''; }, 150);
        });
      });
    })();
  </script>
  <script src="<?= e($baseUrl) ?>/assets/js/front.js"></script>
</body>
</html>
