let currentIndex = 0;
const sections = document.querySelectorAll('.section');
const buttons = document.querySelectorAll('.circle-button');

function showSection(index) {
  sections.forEach((section, i) => {
    section.classList.toggle('hidden', i !== index);
    section.classList.toggle('active', i === index);
  });
  buttons.forEach((button, i) => {
    button.classList.toggle('active', i === index);
  });
}

function nextSection() {
  currentIndex = (currentIndex + 1) % sections.length;
  showSection(currentIndex);
}

buttons.forEach((button, index) => {
  button.addEventListener('click', () => {
    currentIndex = index;
    showSection(currentIndex);
  });
});

setInterval(nextSection, 5000);
