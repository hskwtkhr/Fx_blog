
document.addEventListener('DOMContentLoaded', function () {
    var searchToggle = document.getElementById('search-toggle-btn');
    var searchForm = document.querySelector('.header-search-form-wrapper');

    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function (e) {
            e.preventDefault();
            searchForm.classList.toggle('active');
            var input = searchForm.querySelector('input[type="search"]');
            if (input && searchForm.classList.contains('active')) {
                setTimeout(function () { input.focus(); }, 100);
            }
        });
    }
});
