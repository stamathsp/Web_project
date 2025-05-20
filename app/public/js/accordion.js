document.addEventListener("DOMContentLoaded", function () {
  const toggles = document.querySelectorAll(".accordion-toggle");

  toggles.forEach((toggle) => {
    toggle.addEventListener("click", function () {
      const content = toggle.nextElementSibling;

      // Κλείσιμο όλων
      document.querySelectorAll(".accordion-content").forEach((el) => {
        if (el !== content) el.style.display = "none";
      });

      // Εναλλαγή προβολής του επιλεγμένου
      content.style.display =
        content.style.display === "block" ? "none" : "block";
    });
  });
});
