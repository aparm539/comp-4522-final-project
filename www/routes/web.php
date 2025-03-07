<?php

$router->add("GET", "/", "index.controller.php");

$router->add("GET", "/admin", "/admin/login/create.php");
$router->add("POST", "/admin", "/admin/login/store.php");

$router->add("GET", "/admin/dashboard/photos", "/admin/members-only/photos.php")->restricted();
$router->add("GET", "/admin/dashboard/stats", "/admin/members-only/stats.php")->restricted();

$router->add("GET", "/logout", "/admin/login/destroy.php")->restricted();
