<?php
$pageTitle = "Dashboard";
include_once(__DIR__ . "/../../common/header.php");
include_once(__DIR__ . "/../../common/navbar.php");
?>

<main>
  <div class="dashboardContainer">
    <div class="navMenu"></div>
    <div class="dashboardContent">
      <div class="dashboardColumnOne">
        <div class="welcomeCard"></div>
        <div class="quickCard"></div>
        <div class="knowledgeCard"></div>
        <div class="studentTickets"></div>
      </div>
      <div class="dashboardColumnTwo">
        <div class="welcomeCard"></div>
        <div class="welcomeCard"></div>
        <div class="welcomeCard"></div>
        <div class="welcomeCard"></div>
      </div>
    </div>
  </div>
</main>

<?php include_once(__DIR__ . "/../../common/footer.php"); ?>
