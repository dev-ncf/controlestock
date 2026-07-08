const btn = document.getElementById('dot');
const container = document.getElementById('container');
btn.addEventListener("click", () => {

    container.style.display = 'none';
});

setTimeout(() => {
container.style.display = 'none';
}, 2000);
