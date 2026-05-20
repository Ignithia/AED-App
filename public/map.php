<?php

declare(strict_types=1);

session_start();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AED Studios - Map</title>
    <link rel="stylesheet" href="css/vars.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="components/bottomnavigation.js"></script>
</head>

<body class="dark-theme">
    <div class="search-container">
        <input type="text" placeholder="Van" class="input-field">
        <img src="images/nav-ico/swap.svg" alt="Arrow" class="arrow-icon">
        <input type="text" placeholder="Naar" class="input-field">
    </div>
    <div class="map-container" style="height: 60vh; background: #eee; display: flex; align-items: center; justify-content: center;">
        <p>Interactieve Map (PHP Geïntegreerd)</p>
    </div>
    <bottom-navigation></bottom-navigation>
</body>

</html>