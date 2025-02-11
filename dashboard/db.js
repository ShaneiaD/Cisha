// Get elements
const profileIcon = document.getElementById('profile-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

// Toggle dropdown visibility on click
profileIcon.addEventListener('click', () => {
  dropdownMenu.classList.toggle('active');
});

// Close the dropdown if clicked outside
document.addEventListener('click', (event) => {
  if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
    dropdownMenu.classList.remove('active');
  }
});
