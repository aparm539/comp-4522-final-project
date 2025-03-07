<?php require path_to('views/partials/head.partial.php'); ?>
<?php require path_to('views/partials/topnav.partial.php'); ?>

<main class="table">
    <section class="table_header">
        <div class="header-content">
            <h1>Photo Dashboard</h1>
        </div>
    </section>

    <section class="table_body">
        <table>
            <thead>
                <tr>
                    <th><a href="?sort=ImageID">PhotoID</a></th>
                    <th>Photo</th>
                    <th><a href="?sort=Title">Title</a></th>
                    <th>Continent Code</th>
                    <th><a href="?sort=UserID">UserID</a></th>
                    <th>Name</th>
                    <th>Flag</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($photos)): ?>
                    <tr>
                        <td colspan='8'>No photos available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($photos as $photo): ?>
                        <tr>
                            <td><?= $photo['ImageID'] ?></td>
                            <td>
                                <?php
                                // Construct the Cloudinary URL using the 'Path' column from the database
                                $cloudinary_url = "https://res.cloudinary.com/dppo4xs3o/image/upload/c_auto,h_60,w_60/r_100/" . $photo['Path'];
                                ?>
                                <img src="<?= $cloudinary_url ?>" alt="<?= $photo['Title'] ?>" />
                            </td>
                            <td><?= $photo['Title'] ?></td>
                            <td><?= $photo['ContinentCode'] ?? '-' ?></td> <!-- fallback to '-' -->
                            <td><?= $photo['UserID'] ?></td>
                            <td><?= $photo['FirstName'] ?> <?= $photo['LastName'] ?></td>
                            <td>
                                <form method="post" action="flag.php">
                                    <input type="hidden" name="imageID" value="<?= $photo['ImageID'] ?>">
                                    <input type="hidden" name="flag" value="<?= $photo['IsFlagged'] ? 0 : 1 ?>">
                                    <button type="submit" style="border: none; background: none;">
                                        <img src="../../images/<?= $photo['IsFlagged'] ? 'redflag.png' : 'greenflag.png' ?>" alt="Flag" class="action-icon">
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="delete.php">
                                    <input type="hidden" name="imageID" value="<?= $photo['ImageID'] ?>">
                                    <button type="submit" style="border: none; background: none;">
                                        <img src="../../images/trashcan.png" alt="Delete" class="action-icon">
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php require path_to('views/partials/foot.partial.php'); ?>