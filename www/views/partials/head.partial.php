<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php foreach ($stylesheets as $sheet): ?>
        <link rel="stylesheet" href="/styles/<?= $sheet ?>.css">
    <?php endforeach ?>
    <title><?= $page_title ?></title>
</head>

<body>