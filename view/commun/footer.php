<footer class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <h3>💎 DiamonLux</h3>
            <p>&copy; 2025 diamonlux.store</p>
            <p>Tous droits réservés.</p>
        </div>

        <div class="footer-column">
            <h4>Navigation</h4>
            <ul>
                <li><a href="index.php?page=acceuil">Accueil</a></li>
                <li><a href="index.php?page=profil">Profil</a></li>
                <li><a href="index.php?page=panier">Panier</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h4>Informations</h4>
            <ul class="footer-links">
                <li><a href="view/pages/apropos.html">À propos</a></li>
                <li><a href="view/pages/mentions.html">Mentions légales</a></li>
                <li><a href="view/pages/conditions.html">Conditions d'utilisation</a></li>
                <li><a href="view/pages/contact.html">Contact</a></li>
            </ul>
        </div>
    </div>
</footer>

</div> <!-- fermeture de .wrapper -->
<script>
    const toggle = document.getElementById('darkModeToggle');
    const body = document.body;

    if (localStorage.getItem('darkMode') === 'true') {
        body.classList.add('dark-mode');
    }

    toggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
    });
</script>
</body>

</html>