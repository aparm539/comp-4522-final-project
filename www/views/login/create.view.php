<?php

require path_to('views/partials/head.partial.php'); ?>


<body>
    <div class="wrapper">
        <form action="/admin" method="POST">
            <h1>PhotoVoyage Login</h1>
            <?php if ($error_message): ?>
                <p class="error"><?= $error_message ?></p>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" value="<?= $_SESSION['old_username'] ?? '' ?>">
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password">
            </div>
            <div class="forgot">
                <label><input type="checkbox" name="remember_me"> Remember me for 30 days?</label>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>


    <?php require path_to('views/partials/foot.partial.php'); ?>