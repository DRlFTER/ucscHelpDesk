<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'UCSC Help Desk' ?></title>
  <!-- Opt-in to same-origin View Transitions for cross-document animations -->
  <meta name="view-transition" content="same-origin">

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
      <img class="loginGradient" src="/assets/imgs/loginGradient.jpg" alt="Gradient Background">

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
    .loginGradient {
      position: fixed;
      inset: 0;           
      width: 100vw;
      height: 100vh;
      object-fit: cover;
      z-index: -10;
      opacity: 0.8;  
    }
  </style>
  
    <?= $content ?>

<script>
  const loader = document.getElementById("pageLoader");
  let loaderTimeout;

  document.addEventListener("DOMContentLoaded", () => {
    loader.style.display = "none";
  });

  window.addEventListener("beforeunload", () => {
    loaderTimeout = setTimeout(() => {
      loader.style.display = "flex";
    }, 1000);
  });

  window.addEventListener("load", () => {
    clearTimeout(loaderTimeout);
    loader.style.display = "none";
  });

  window.addEventListener("pageshow", (e) => {
    clearTimeout(loaderTimeout);
    loader.style.display = "none";
  });
</script>

</body>
</html>