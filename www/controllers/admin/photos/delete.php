<?php

require path_to('database/DatabaseQueries');

// Handle delete logic.
if (isset($_POST['delete_photo_id'])) {
    $photoID = $_POST['delete_photo_id'];

    $dbQueries = new DatabaseQueries();
    $dbQueries->delete_photo($photoID);

    redirect('/admin/dashboard/photos');
}
