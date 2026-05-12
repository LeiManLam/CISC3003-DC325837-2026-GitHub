/**
 * Scenario C — browser validation + Ajax email check + home buttons.
 */
(function () {
  function showInline(el, text, ok) {
    if (!el) {
      return;
    }
    el.hidden = false;
    el.textContent = text;
    el.className = "hint " + (ok ? "ok" : "err");
  }

  function validateSignup(form) {
    var ok = true;
    var name = form.querySelector("#full_name");
    var email = form.querySelector("#email");
    var p1 = form.querySelector("#password");
    var p2 = form.querySelector("#password2");

    if (!name || name.value.trim().length < 2) {
      ok = false;
    }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      ok = false;
    }
    if (!p1 || p1.value.length < 8) {
      ok = false;
    }
    if (!p2 || p2.value !== p1.value) {
      ok = false;
    }
    return ok;
  }

  function validateLogin(form) {
    var email = form.querySelector("#email");
    var p = form.querySelector("#password");
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      return false;
    }
    if (!p || p.value.length < 1) {
      return false;
    }
    return true;
  }

  function checkEmailAvailable(email, msgEl) {
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showInline(msgEl, "", true);
      return;
    }
    fetch("api/check_email.php?email=" + encodeURIComponent(email), {
      headers: { Accept: "application/json" },
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (!data || !data.ok) {
          showInline(msgEl, "Could not check email.", false);
          return;
        }
        if (data.available) {
          showInline(msgEl, "This email is available.", true);
        } else {
          showInline(msgEl, "This email is already registered.", false);
        }
      })
      .catch(function () {
        showInline(msgEl, "Network error checking email.", false);
      });
  }

  document.addEventListener("DOMContentLoaded", function () {
    var signup = document.getElementById("signup-form");
    if (signup) {
      signup.addEventListener("submit", function (e) {
        if (!validateSignup(signup)) {
          e.preventDefault();
          alert("Please fix the form: name, valid email, matching passwords (8+ chars).");
        }
      });
      var emailInput = signup.querySelector("#email");
      var ajaxMsg = document.getElementById("email-ajax-msg");
      if (emailInput && ajaxMsg) {
        emailInput.addEventListener("blur", function () {
          checkEmailAvailable(emailInput.value.trim(), ajaxMsg);
        });
      }
    }

    var login = document.getElementById("login-form");
    if (login) {
      login.addEventListener("submit", function (e) {
        if (!validateLogin(login)) {
          e.preventDefault();
          alert("Enter a valid email and password.");
        }
      });
    }

    var su = document.getElementById("btn-signup");
    var si = document.getElementById("btn-signin");
    if (su) {
      su.addEventListener("click", function () {
        window.location.href = "register.php";
      });
    }
    if (si) {
      si.addEventListener("click", function () {
        window.location.href = "login.php";
      });
    }
  });
})();
