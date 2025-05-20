document.addEventListener("DOMContentLoaded", () => {
  const headers = document.querySelectorAll(".accordion-header");

  if (headers.length === 0) {
    console.warn("Δεν βρέθηκαν headers για το accordion.");
    return;
  }

  headers.forEach(header => {
    header.addEventListener("click", () => {
      const content = header.nextElementSibling;
      content.classList.toggle("active");

      // Προαιρετικά: Κλείσε τα υπόλοιπα
      document.querySelectorAll(".accordion-content").forEach(other => {
        if (other !== content) other.classList.remove("active");
      });
    });
  });
});
