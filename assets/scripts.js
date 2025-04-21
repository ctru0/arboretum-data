//collapsable navigation buttons
console.log("JS is running!");

document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('menu-toggle');
    const navLinks = document.querySelector('.nav-links');
  
    if (toggleButton && navLinks) {
      toggleButton.addEventListener('click', function () {
        navLinks.classList.toggle('active');
      });
    }
  });
  