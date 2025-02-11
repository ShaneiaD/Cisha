// Search functionality
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');

searchButton.addEventListener('click', () => {
  const query = searchInput.value.toLowerCase();
  const courses = document.querySelectorAll('.course-card');

  courses.forEach(course => {
    const title = course.querySelector('h3').textContent.toLowerCase();
    if (title.includes(query)) {
      course.style.display = '';
    } else {
      course.style.display = 'none';
    }
  });
});
