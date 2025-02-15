const slider = document.querySelector('.slider');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');

let currentIndex = 0;

function updateSlider() {
    const slideWidth = slider.querySelector('.slider-item').offsetWidth + 16; // Include the gap
    slider.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}

prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateSlider();
    }
});

nextBtn.addEventListener('click', () => {
    const maxIndex = slider.children.length - 4; // Visible items are 4
    if (currentIndex < maxIndex) {
        currentIndex++;
        updateSlider();
    }
});
