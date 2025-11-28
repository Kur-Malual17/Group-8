(function () {
  // Mobile navigation: toggle nav open/closed
  function bindHamburger(el) {
    var btns = document.querySelectorAll('.hamburger');
    btns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var header = btn.closest('header');
        if (!header) return;
        var expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', (!expanded).toString());
        header.classList.toggle('nav-open', !expanded);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindHamburger);
  } else {
    bindHamburger();
  }
})();
