/**
 * Scenario B — client-side validation (contact form) + Sign up / Sign in buttons.
 */
(function () {
  function showErr(input, msg) {
    var id = input.id + "-err";
    var el = document.getElementById(id);
    if (!el) {
      el = document.createElement("p");
      el.id = id;
      el.className = "alert alert-error";
      el.style.marginTop = "0.35rem";
      input.insertAdjacentElement("afterend", el);
    }
    el.textContent = msg;
    el.hidden = false;
  }

  function clearErr(input) {
    var el = document.getElementById(input.id + "-err");
    if (el) {
      el.hidden = true;
      el.textContent = "";
    }
  }

  function validateContact(form) {
    var ok = true;
    var name = form.querySelector("#name");
    var email = form.querySelector("#email");
    var subject = form.querySelector("#subject");
    var message = form.querySelector("#message");

    if (name.value.trim().length < 2) {
      showErr(name, "Name must be at least 2 characters.");
      ok = false;
    } else {
      clearErr(name);
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
      showErr(email, "Enter a valid email.");
      ok = false;
    } else {
      clearErr(email);
    }

    if (!subject.value.trim()) {
      showErr(subject, "Subject is required.");
      ok = false;
    } else {
      clearErr(subject);
    }

    if (message.value.trim().length < 10) {
      showErr(message, "Message must be at least 10 characters.");
      ok = false;
    } else {
      clearErr(message);
    }

    return ok;
  }

  document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("contact-form");
    if (form) {
      form.addEventListener("submit", function (e) {
        if (!validateContact(form)) {
          e.preventDefault();
        }
      });
    }

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
