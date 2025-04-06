document.addEventListener("DOMContentLoaded", function () {
  // Auto-dismiss alerts after 5 seconds
  var alerts = document.querySelectorAll(".alert");
  alerts.forEach(function (alert) {
    setTimeout(function () {
      var bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });

  // Confirm before delete/restore actions
  document
    .querySelectorAll(".btn-delete, .btn-restore")
    .forEach(function (button) {
      button.addEventListener("click", function (e) {
        if (!confirm(button.getAttribute("data-confirm") || "Are you sure?")) {
          e.preventDefault();
        }
      });
    });

  // Form validation
  var forms = document.querySelectorAll(".needs-validation");
  forms.forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add("was-validated");
      },
      false
    );
  });
});
