<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? 'UCSC Help Desk' ?></title>

    <meta name="view-transition" content="same-origin" />

    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="/assets/images/logo-min.svg"
    />

    <link rel="stylesheet" href="/css/global/components.css" />

    <?= $head ?? '' ?>

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
        to {
          transform: rotate(360deg);
        }
      }

      .loginGradientHolder {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: white;
        z-index: -1;
      }

      .loginGradient {
        position: relative;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -10;
        opacity: 0.6;
        scale: 1.5;
      }
    </style>
  </head>

  <body>

    <div id="pageLoader" style="display: none;">
      <div class="loader"></div>
    </div>

    <div class="loginGradientHolder">
      <img
        class="loginGradient"
        src="/assets/imgs/loginGradient.jpg"
        alt="Gradient Background"
      />
    </div>

   <?php
     // Determine if we're on login or register pages (hide sidenav)
     $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
     $hideSidenav = preg_match('#^/(login|register|auth/login|auth/register)#i', $currentPath);
   ?>

   <?php if ($hideSidenav): ?>
     <main class="mainContent mainContentFull">
       <?= $content ?>
     </main>
   <?php else: ?>
   <div class="appLayout">
     <div class="sidenavWrapper">
       <?php include_once(__DIR__ . "/../../views/common/navbar.php"); ?>
     </div>
     <main class="mainContent">
       <?= $content ?>
     </main>
   </div>
   <?php endif; ?>


   

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

      window.addEventListener("pageshow", () => {
        clearTimeout(loaderTimeout);
        loader.style.display = "none";
      });
    </script>
  </body>
</html>
