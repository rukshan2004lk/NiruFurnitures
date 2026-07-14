<?php
/**
 * NiRu-Furnitures — Reusable <head> block
 * -------------------------------------------------------
 * Variables that can be set BEFORE including this file:
 *   $pageTitle       (string)  — document title
 *   $pageDescription (string)  — meta description
 *   $extraCss        (array)   — additional CSS files
 */

$pageTitle       = $pageTitle       ?? SITE_NAME . ' — Premium Furniture';
$pageDescription = $pageDescription ?? getSetting('meta_description', 'NiRu-Furnitures — Premium quality furniture for every room.');
$extraCss        = $extraCss        ?? [];
$bodyClass       = $bodyClass       ?? '';
$canonicalUrl    = SITE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- SEO -->
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="robots"      content="index, follow">
    <link rel="canonical"    href="<?= e($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:title"       content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= e($canonicalUrl) ?>">
    <meta property="og:site_name"   content="<?= e(SITE_NAME) ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.ico" type="image/x-icon">

    <!-- Google Fonts: Poppins + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- AOS — Animate On Scroll -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/variables.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/typography.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/responsive.css">

    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= e($css) ?>">
    <?php endforeach; ?>
</head>
<body class="<?= e($bodyClass) ?>">
