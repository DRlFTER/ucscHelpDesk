<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars($flash['type']) ?>">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="logInContainer">
    <div class="logInBox">
        <div class="imgsHolder">
            <div class="imgCarousel">
                <div class="imgGradient"></div>
                <img src="/public/assets/imgs/3.png" alt="Login Image 1" class="carouselImage active">
                <div class="textCarousel">
                    <h2></h2>
                    <div class="pillContainer">
                        <div class="pill pillActive"></div>
                        <div class="pill"></div>
                        <div class="pill"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="formHolder">
            <div class="formContainer">
             <div class="logInTitles">
                <h2 class="titleText">Sign in</h2>
                <p class="paraText">Don't have an account? <a href="/public/register">Sign up</a></p>
             </div><form action="/public/login" method="POST">
              <div class="formFields">
                 <input type="email" id="email" name="email" placeholder="Enter email" required>
                 <input type="password" id="password" name="password" placeholder="Enter password" required>
             </div>
             <div class="btnHolder">
                <button class="btnPrimary btnPrimaryText" type="submit">Log in</button>
             </div></form>
            </div>
          
        </div>
    </div>
</div>
<script src="/app/views/login/login.js"></script>