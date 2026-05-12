/**
 * Scenario A — Sign up / Sign in button helpers (client-side UX only).
 */
(function () {
  document.addEventListener("DOMContentLoaded", function () {
    var signup = document.getElementById("btn-signup");
    var signin = document.getElementById("btn-signin");
    if (signup) {
      signup.addEventListener("click", function () {
        window.location.href = "register.php";
      });
    }
    if (signin) {
      signin.addEventListener("click", function () {
        window.location.href = "login.php";
      });
    }
  });
})();
