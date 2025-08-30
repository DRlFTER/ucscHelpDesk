<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'UCSC Help Desk' ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo-min.svg">

    <!-- Global styles -->
    <link rel="stylesheet" href="/css/global/components.css">

    <!-- Page-specific head content -->
    <?= $head ?? '' ?>
</head>
<body>
    <?php include_once(__DIR__ . "/../../views/common/navbar.php"); ?>
      <div id="pageLoader" style="display:none;">
        <div class="loader"></div>
      </div>

  <style>
    #pageLoader {
      position: fixed;
      inset: 0;
      background: rgba(255, 255, 255, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    .loader {
      width: 50px;
      height: 50px;
      border: 5px solid #ccc;
      border-top: 5px solid #8c8cf9;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
  
    <?= $content ?>

     <script>
    const loader = document.getElementById("pageLoader");

    window.addEventListener("beforeunload", () => {
      loader.style.display = "flex";
    });

    window.addEventListener("load", () => {
      loader.style.display = "none";
    });
  </script>
</body>
</html>