<?php

require path_to('database/DatabaseQueries');

// Handle flagging/unflagging logic.
if (isset($_POST['flag_photo_id'])) {
    $photoID = $_POST['flag_photo_id'];
    $isFlagged = $_POST['is_flagged'];

    $dbQueries = new DatabaseQueries();

    if ($isFlagged == '1') {
        $dbQueries->unflag_photo($photoID);
    } else {
        $dbQueries->flag_photo($photoID);
    }

    redirect('/admin/dashboard/photos');
}
