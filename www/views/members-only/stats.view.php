<?php
require path_to('views/partials/head.partial.php');
?>
<main>
    <section class="glass">
        <div class="dashboard">
            <div class="user">
                <h3>User Name</h3>
                <p>Pro Member</p>
            </div>
            <div class="links">
                <div class="link">
                    <h2><a href="photos">Photo Dashboard</a></h2>
                </div>
                <div class="link">
                    <h2><a href="/admin">Log out</a></h2>
                </div>
            </div>
        </div>
        <div class="games">
            <div class="status">
                <h1>Statistics</h1>
            </div>
            <div class="cards">
                <div class="card">
                    <div class="card-info">
                        <h2>Most Popular City</h2>
                        <p><?= $mostPopularCity['CityName'] ?? 'N/A'; ?></p>
                        <div class="progress"></div>
                    </div>
                    <h2 class="percentage">📍</h2>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h2>My Photo Dashboard</h2>
                        <p><a href="photos">Go to Dashboard</a></p>
                        <div class="progress"></div>
                    </div>
                    <h2 class="percentage">Go!</h2>
                </div>
                <div class="card">
                    <div class="card-info">
                        <h2>Number of Pictures Available</h2>
                        <p><?= $totalPhotos; ?></p>
                        <div class="progress"></div>
                    </div>
                    <h1 class="percentage">🎥</h1>
                </div>
                <div class="card">
                    <img src="./images/flag.png" alt="" />
                    <div class="card-info">
                        <h2>Flagged Users</h2>
                        <ul>
                            <?php if (!empty($flaggedUsers)): ?>
                                <?php foreach ($flaggedUsers as $user): ?>
                                    <li><?php echo $user['FirstName'] . ' ' . $user['LastName']; ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No flagged users</li>
                            <?php endif; ?>
                        </ul>
                        <div class="progress"></div>
                    </div>
                    <h2 class="percentage">⚠️</h2>
                </div>
            </div>
        </div>
    </section>
</main>
<div class="circle1"></div>
<div class="circle2"></div>


<?php require path_to('views/partials/foot.partial.php'); ?>