const FILE_TYPES = ['gif', 'jpg', 'jpeg', 'png'];
const overlay = document.querySelector('.overlay');
const popup = document.querySelector('.pop-up');
const imgPreviewElement = document.querySelector('.avatar-preview');

const actionButtons = document.querySelectorAll('.action-btn');

actionButtons.forEach(function (el) {
    el.addEventListener('click', function (evt) {
        const modalType = evt.target.dataset.action;
        const modal = document.querySelector('.pop-up--' + modalType);
        modal.classList.remove('pop-up--close');
        modal.classList.add('pop-up--open');
        overlay.classList.add('db');
    })
});

const buttonsClose = document.querySelectorAll('.button--close');

buttonsClose.forEach(function (el) {
    el.addEventListener('click', function (evt) {
        const modalOpen = document.querySelector('.pop-up--open');
        modalOpen.classList.remove('pop-up--open');
        modalOpen.classList.add('pop-up--close');
        overlay.classList.remove('db');

    })
});

let buttonInput = document.querySelector('#button-input');

if (buttonInput) {
    buttonInput.addEventListener('change', function (evt) {
        const file = evt.target.files[0];
        const fileName = file.name.toLowerCase();

        const matches = FILE_TYPES.some(function (it) {
            return fileName.endsWith(it);
        });
        if (matches) {
            const reader = new FileReader();
            reader.addEventListener('load', function () {
                imgPreviewElement.src = reader.result;
            });
            reader.readAsDataURL(file);
        }
    });
}

const ratingView = document.querySelector(".active-stars");

if (ratingView) {
    ratingView.addEventListener("click", event => {
        const stars = Array.from(ratingView.children);
        const clickedStar = event.target;

        stars.forEach(star => star.classList.remove("fill-star"));

        const index = stars.indexOf(clickedStar);

        if (index === -1) return;

        for (let i = 0; i <= index; i++) {
            stars[i].classList.add("fill-star");
        }

        const inputField = document.getElementById('acceptance-form-rate');
        if (inputField) {
            inputField.value = index + 1;
        }
    });
}
