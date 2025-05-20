document.addEventListener("DOMContentLoaded", () => {
  const body = document.body;
  const toggle = document.getElementById("theme-toggle");

  const saved = localStorage.getItem("theme");
  if (saved === "dark") body.classList.add("dark-theme");

  toggle.addEventListener("click", () => {
    body.classList.toggle("dark-theme");
    const mode = body.classList.contains("dark-theme") ? "dark" : "light";
    localStorage.setItem("theme", mode);
  });
});
