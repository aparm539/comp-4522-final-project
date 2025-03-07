<nav id="topnav">
    <div class="logo-name">
        <h1>Photo Voyage</h1>
    </div>
    <ul>
        <li class="<?= uri_matches("/photos") ? "selected" : "" ?>"><a href="/photos">Photos</a></li>
        <li class="<?= uri_matches("/stats") ? "selected" : "" ?>"><a href="/stats">Stats</a></li>
        <li><a href="/admin">Logout</a></li>
    </ul>
</nav>