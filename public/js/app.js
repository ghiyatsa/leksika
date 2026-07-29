document.addEventListener('DOMContentLoaded', () => {
    // Theme toggle
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            document.documentElement.classList.add('no-transitions');
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            window.getComputedStyle(document.documentElement).opacity;
            setTimeout(() => {
                document.documentElement.classList.remove('no-transitions');
            }, 0);
        });
    }

    // Live Clock Widget Ticking
    const liveClock = document.getElementById('live-clock');
    if (liveClock) {
        setInterval(() => {
            if (liveClock.offsetParent === null) return;
            const now = new Date();
            const hrs = String(now.getHours()).padStart(2, '0');
            const mins = String(now.getMinutes()).padStart(2, '0');
            const secs = String(now.getSeconds()).padStart(2, '0');
            liveClock.textContent = `${hrs}.${mins}.${secs}`;
        }, 1000);
    }

    // Mobile sidebar navigation menu toggle
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');

    if (sidebar && sidebarToggle) {
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('active');
        });
    }

    if (sidebar && sidebarClose) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    document.addEventListener('click', (e) => {
        if (sidebar && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && (!sidebarToggle || !sidebarToggle.contains(e.target))) {
                sidebar.classList.remove('active');
            }
        }
    });

    // Command Palette Modal Actions
    const cmdPalette = document.getElementById('cmd-palette');
    const cmdInput = document.getElementById('cmd-input');
    const cmdOpenBtn = document.getElementById('cmd-open-btn');
    let cmdOpenerEl = null;

    function togglePalette(show) {
        if (!cmdPalette) return;
        if (show) {
            cmdOpenerEl = document.activeElement;
            cmdPalette.classList.add('active');
            cmdPalette.removeAttribute('hidden');
            if (cmdInput) cmdInput.focus();
        } else {
            cmdPalette.classList.remove('active');
            cmdPalette.setAttribute('hidden', '');
            if (cmdInput) cmdInput.value = '';
            const items = document.querySelectorAll('.cmd-item');
            items.forEach(item => item.style.display = 'flex');
            if (cmdOpenerEl) cmdOpenerEl.focus();
        }
    }

    if (cmdPalette) {
        cmdPalette.setAttribute('hidden', '');
        cmdPalette.addEventListener('keydown', (e) => {
            if (!cmdPalette.classList.contains('active')) return;
            if (e.key !== 'Tab') return;
            const focusable = Array.from(cmdPalette.querySelectorAll(
                'input, button, a[href], [tabindex]:not([tabindex="-1"])'
            )).filter(el => !el.disabled);
            if (focusable.length === 0) return;
            const first = focusable[0];
            const last  = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) {
                e.preventDefault();
                last.focus();
            } else if (!e.shiftKey && document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }
        });
    }

    if (cmdOpenBtn) {
        cmdOpenBtn.addEventListener('click', (e) => {
            e.preventDefault();
            togglePalette(true);
        });
    }

    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (cmdPalette) {
                const isActive = cmdPalette.classList.contains('active');
                togglePalette(!isActive);
            }
        }
        if (e.key === 'Escape') {
            togglePalette(false);
        }
    });

    if (cmdPalette) {
        cmdPalette.addEventListener('click', (e) => {
            if (e.target === cmdPalette) {
                togglePalette(false);
            }
        });
    }

    if (cmdInput) {
        cmdInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.cmd-item');
            items.forEach(item => {
                const textVal = item.querySelector('span').textContent.toLowerCase();
                if (textVal.includes(val)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Global data-confirm pattern
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form.hasAttribute('data-confirm')) return;
        e.preventDefault();
        const msg = form.getAttribute('data-confirm');
        if (confirm(msg)) {
            form.removeAttribute('data-confirm');
            form.submit();
        }
    });
});
