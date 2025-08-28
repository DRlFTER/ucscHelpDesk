<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My Website' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo-min.svg">

    <!-- Global styles -->
    <link rel="stylesheet" href="../common/css/components.css">

    <!-- Page-specific head content -->
    <?= $head ?? '' ?>
</head>
<body>
    <?= $content ?>
</body>
</html>