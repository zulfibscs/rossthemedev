(function(){
    'use strict';

    var OPEN_CLASS = 'search-overlay--open';

    function getOverlay(){
        return document.querySelector('.search-overlay');
    }

    function openSearch(){
        var overlay = getOverlay();
        if(!overlay) return;
        overlay.classList.add(OPEN_CLASS);
        overlay.setAttribute('aria-hidden','false');
        var input = overlay.querySelector('input[type="search"], input[name="s"]');
        if(input){ try { input.focus(); input.select(); } catch(e){} }
    }

    function closeSearch(){
        var overlay = getOverlay();
        if(!overlay) return;
        overlay.classList.remove(OPEN_CLASS);
        overlay.setAttribute('aria-hidden','true');
    }

    // Toggle by clicking search-toggle button
    document.addEventListener('click', function(e){
        var btn = e.target.closest ? e.target.closest('.search-toggle') : null;
        if(btn){ e.preventDefault(); openSearch(); return; }

        // Close when clicking outside the inner dialog
        if(e.target.closest && e.target.closest('.search-overlay')){
            var inner = e.target.closest('.search-overlay-inner');
            if(!inner){ closeSearch(); }
        }

        if(e.target.closest && e.target.closest('.search-overlay-close')){
            e.preventDefault(); closeSearch();
        }
    }, false);

    // Close on Escape
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape' || e.key === 'Esc'){
            closeSearch();
        }
    });

})();
