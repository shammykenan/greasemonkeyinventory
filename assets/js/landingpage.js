/* ===========================
   LOGIN MODAL JAVASCRIPT
   login-modal.js
   =========================== */

(function () {
    'use strict';

    /* ---- DOM refs ---- */
    const overlay     = document.getElementById('loginModalOverlay');
    const modalBox    = document.getElementById('loginModalBox');
    const openBtns    = document.querySelectorAll('[data-open-login]');
    const closeBtn    = document.getElementById('loginModalClose');
    const panelLogin  = document.getElementById('lmPanelLogin');
    const panelForgot = document.getElementById('lmPanelForgot');
    const eyeToggle   = document.getElementById('lmEyeToggle');
    const pwInput     = document.getElementById('lmPassword');

    /* ---- Open / Close ---- */
    function openModal() {
        overlay.classList.add('active');
        modalBox.classList.add('active');
        document.body.style.overflow = 'hidden';
        // focus username after animation
        setTimeout(() => {
            const u = document.getElementById('lmUsername');
            if (u) u.focus();
        }, 420);
    }

    function closeModal() {
        overlay.classList.remove('active');
        modalBox.classList.remove('active');
        document.body.style.overflow = '';
        // reset to login panel after close
        setTimeout(showLoginPanel, 350);
    }

    /* ---- Panel switching ---- */
    const headerLogin  = document.getElementById('lmPanelLoginHeader');
    const headerForgot = document.getElementById('lmPanelForgotHeader');

    function showLoginPanel() {
        panelLogin.classList.add('active');
        panelForgot.classList.remove('active');
        if (headerLogin)  headerLogin.classList.add('active');
        if (headerForgot) headerForgot.classList.remove('active');
    }

    function showForgotPanel() {
        panelForgot.classList.add('active');
        panelLogin.classList.remove('active');
        if (headerForgot) headerForgot.classList.add('active');
        if (headerLogin)  headerLogin.classList.remove('active');
        setTimeout(() => {
            const e = document.getElementById('lmEmail');
            if (e) e.focus();
        }, 320);
    }

    /* ---- Password toggle ---- */
    function togglePassword() {
        if (!pwInput || !eyeToggle) return;
        const isHidden = pwInput.type === 'password';
        pwInput.type = isHidden ? 'text' : 'password';
        eyeToggle.textContent = isHidden ? '👁️‍🗨️' : '👁️';
    }

    /* ---- Input focus micro-interaction ---- */
    function bindInputFocus() {
        document.querySelectorAll('.lm-input').forEach(input => {
            input.addEventListener('focus', () => {
                input.closest('.lm-input-wrap').style.transform = 'scale(1.015)';
                input.closest('.lm-input-wrap').style.transition = 'transform 0.2s ease';
            });
            input.addEventListener('blur', () => {
                input.closest('.lm-input-wrap').style.transform = 'scale(1)';
            });
        });
    }

    /* ---- Auto-hide success alerts ---- */
    function autoHideSuccessAlerts() {
        setTimeout(() => {
            document.querySelectorAll('.lm-alert-success').forEach(el => {
                el.style.transition = 'opacity 0.3s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 320);
            });
        }, 3000);
    }

    /* ---- Keyboard: ESC to close ---- */
    function onKeydown(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeModal();
        }
    }

    /* ---- Click outside modal box to close ---- */
    function onOverlayClick(e) {
        if (e.target === overlay) closeModal();
    }

    /* ---- Expose panel switchers to inline onclick (for navbar login link) ---- */
    window.lmOpenModal      = openModal;
    window.lmCloseModal     = closeModal;
    window.lmShowForgot     = showForgotPanel;
    window.lmShowLogin      = showLoginPanel;
    window.lmTogglePassword = togglePassword;

    /* ---- Init ---- */
    function init() {
        // Bind open buttons
        openBtns.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        }));

        if (closeBtn)  closeBtn.addEventListener('click', closeModal);
        if (overlay)   overlay.addEventListener('click', onOverlayClick);
        if (eyeToggle) eyeToggle.addEventListener('click', togglePassword);

        document.addEventListener('keydown', onKeydown);

        bindInputFocus();
        autoHideSuccessAlerts();

        // If PHP set an error, open modal + shake + highlight inputs
        const serverError = document.getElementById('lmServerError');
        if (serverError) {
            openModal();

            // Determine error type from message text
            const msg = serverError.textContent || '';
            const isRateLimit = msg.toLowerCase().includes('too many');

            // Style the alert differently for rate-limit vs wrong credentials
            if (isRateLimit) {
                serverError.classList.add('lm-alert-ratelimit');
                serverError.classList.remove('lm-alert-error');
            }

            // Shake the modal box after it finishes opening
            setTimeout(() => {
                modalBox.classList.add('lm-shake');
                modalBox.addEventListener('animationend', () => {
                    modalBox.classList.remove('lm-shake');
                }, { once: true });
            }, 450);

            // Red border on inputs (only for wrong credentials, not rate limit)
            if (!isRateLimit) {
                const usernameInput = document.getElementById('lmUsername');
                const passwordInput = document.getElementById('lmPassword');

                [usernameInput, passwordInput].forEach(input => {
                    if (!input) return;
                    input.classList.add('lm-input-error');

                    // Remove error state as soon as user starts typing
                    input.addEventListener('input', () => {
                        input.classList.remove('lm-input-error');
                        input.classList.add('lm-input-typing');
                        // Also fade out the error alert
                        serverError.style.transition = 'opacity 0.4s ease';
                        serverError.style.opacity = '0.5';
                    }, { once: true });
                });

                // Focus password field if username already has a value
                setTimeout(() => {
                    const u = document.getElementById('lmUsername');
                    if (u && u.value.trim() !== '') {
                        document.getElementById('lmPassword')?.focus();
                    }
                }, 480);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();