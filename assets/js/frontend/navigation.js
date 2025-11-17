(function(){
    // Simple mobile menu toggle
    function initMobileMenu(){
        var btn = document.querySelector('.menu-toggle');
        var nav = document.querySelector('.header-navigation');
        if(!btn || !nav) return;
        btn.addEventListener('click', function(e){
            e.preventDefault();
            nav.classList.toggle('open');
            btn.classList.toggle('is-open');
        });
    }

    // Submenu toggles for accessibility
    function initSubmenuToggles(){
        var items = document.querySelectorAll('.primary-menu li.menu-item-has-children');
        items.forEach(function(li){
            var btn = li.querySelector('.submenu-toggle');
            if(!btn){
                btn = document.createElement('button');
                btn.className = 'submenu-toggle';
                btn.setAttribute('aria-expanded','false');
                btn.innerHTML = '<span class="screen-reader-text">Toggle submenu</span>';
                li.insertBefore(btn, li.querySelector('ul'));
            }
            btn.addEventListener('click', function(){
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
                li.classList.toggle('submenu-open');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
        initMobileMenu();
        initSubmenuToggles();
    });
})();
