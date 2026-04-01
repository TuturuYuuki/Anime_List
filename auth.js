(function () {
    const WEBAUTHN_RP_ID = (location.hostname === '127.0.0.1' || location.hostname === 'localhost')
        ? 'localhost'
        : location.hostname;

    const state = {
        user: null,
        hasBiometric: false,
        authGateEl: null,
        toastEl: null,
        toastHideTimer: null,
        profileModalEl: null,
        profilePasswordRevealTimer: null,
        profilePasswordCountdownTimer: null,
        profilePasswordUnlockedUntil: 0,
        profilePasswordVerifying: false,
        profilePasswordsVisible: false,
        cropper: null,
        pendingProfileDataUrl: '',
        appMainEl: null,
        appInitLoaderEl: null
    };

    const nativeFetch = window.fetch.bind(window);
    window.fetch = async function (input, init = {}) {
        const withCreds = { credentials: 'same-origin', ...init };
        const response = await nativeFetch(input, withCreds);

        try {
            const url = typeof input === 'string' ? input : (input && input.url ? input.url : '');
            const isApi = url.includes('api.php');
            const isAuthAction = /action=(login|signup|forgot_password|reset_password|session_status|webauthn_begin_login|webauthn_finish_login)/.test(url);
            if (isApi && response.status === 401 && !isAuthAction) {
                const msg = 'Sesi berakhir. Silakan login ulang.';
                showAuthGate(true, msg);
                showGlobalToast(msg);
            }
        } catch (e) {
            // Ignore parser errors for URL detection.
        }

        return response;
    };

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function countWords(value) {
        return String(value || '')
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .length;
    }

    function avatarFallback(seed) {
        return 'https://api.dicebear.com/7.x/adventurer/svg?seed=' + encodeURIComponent(seed || 'vault-user');
    }

    function addStyles() {
        const style = document.createElement('style');
        style.textContent = `
            body.auth-locked main, body.auth-locked nav, body.auth-locked #waifu-bg {
                filter: blur(8px) saturate(0.8);
                pointer-events: none;
                user-select: none;
            }

            #auth-gate {
                position: fixed;
                inset: 0;
                z-index: 800;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background:
                    radial-gradient(circle at 15% 20%, rgba(236, 72, 153, 0.35), transparent 40%),
                    radial-gradient(circle at 85% 75%, rgba(59, 130, 246, 0.3), transparent 40%),
                    rgba(10, 7, 22, 0.8);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
            }

            #auth-gate.open {
                display: flex;
            }

            .auth-glass {
                width: min(940px, 100%);
                border-radius: 22px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.04));
                box-shadow: 0 30px 70px rgba(0, 0, 0, 0.45);
                overflow: hidden;
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .auth-hero {
                padding: 32px;
                background:
                    linear-gradient(145deg, rgba(220, 38, 38, 0.22), rgba(124, 58, 237, 0.15));
                border-right: 1px solid rgba(255, 255, 255, 0.12);
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .auth-forms {
                padding: 28px;
            }

            .auth-title {
                font-family: 'Nunito', sans-serif;
                font-weight: 800;
                color: #f5d0fe;
                letter-spacing: 0.02em;
            }

            .auth-sub {
                color: rgba(255, 255, 255, 0.75);
                font-size: 0.9rem;
                line-height: 1.6;
            }

            .auth-kicker {
                font-size: 0.76rem;
                color: rgba(251, 207, 232, 0.9);
            }

            .auth-hero-art {
                width: 86px;
                height: 86px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.28);
                object-fit: cover;
                align-self: flex-end;
                margin-top: 4px;
                background: rgba(255, 255, 255, 0.08);
                flex-shrink: 0;
            }

            .auth-input {
                width: 100%;
                border-radius: 12px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(15, 10, 30, 0.55);
                color: #fff;
                padding: 10px 12px;
                outline: none;
                margin-bottom: 10px;
            }

            .auth-input-wrap {
                position: relative;
                margin-bottom: 10px;
            }

            .auth-input-wrap .auth-input {
                margin-bottom: 0;
                padding-right: 44px;
            }

            .auth-eye {
                position: absolute;
                right: 8px;
                top: 50%;
                transform: translateY(-50%);
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.08);
                color: #f5d0fe;
                width: 30px;
                height: 30px;
                border-radius: 8px;
                font-size: 14px;
                line-height: 1;
                cursor: pointer;
            }

            .auth-eye svg {
                width: 16px;
                height: 16px;
                display: block;
                margin: 0 auto;
            }

            .auth-input:focus {
                border-color: rgba(236, 72, 153, 0.7);
                box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.16);
            }

            .auth-btn {
                width: 100%;
                border-radius: 12px;
                padding: 10px 12px;
                font-weight: 700;
                border: none;
                cursor: pointer;
                color: #fff;
            }

            .auth-btn-main {
                background: linear-gradient(135deg, #ef4444, #ec4899);
            }

            .auth-btn-alt {
                background: rgba(59, 130, 246, 0.2);
                border: 1px solid rgba(59, 130, 246, 0.3);
            }

            .auth-btn-ghost {
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .auth-message {
                margin-top: 10px;
                font-size: 0.82rem;
                min-height: 20px;
                color: #fca5a5;
            }

            .vault-toast {
                position: fixed;
                top: 14px;
                left: 50%;
                transform: translate(-50%, -14px);
                z-index: 1200;
                min-width: 280px;
                max-width: min(92vw, 640px);
                border-radius: 14px;
                border: 1px solid rgba(255, 255, 255, 0.24);
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.9), rgba(91, 33, 182, 0.9));
                color: #fff;
                padding: 12px 14px;
                font-size: 0.9rem;
                line-height: 1.45;
                box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
                opacity: 0;
                pointer-events: none;
                transition: opacity 220ms ease, transform 220ms ease;
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
            }

            .vault-toast.open {
                opacity: 1;
                transform: translate(-50%, 0);
            }

            .auth-tab-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-bottom: 12px;
            }

            .auth-tab {
                border-radius: 10px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.05);
                color: rgba(255, 255, 255, 0.8);
                font-weight: 700;
                padding: 8px;
                cursor: pointer;
            }

            .auth-tab.active {
                background: rgba(239, 68, 68, 0.24);
                color: #fff;
                border-color: rgba(239, 68, 68, 0.55);
            }

            .hidden-auth {
                display: none;
            }

            .profile-nav-btn {
                width: 36px;
                height: 36px;
                border-radius: 999px;
                border: 2px solid rgba(255, 255, 255, 0.25);
                overflow: hidden;
                background: rgba(255, 255, 255, 0.08);
                cursor: pointer;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .profile-nav-btn img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            #profile-settings-modal {
                z-index: 700;
            }

            #profile-crop-modal {
                z-index: 750;
            }

            @media (max-width: 900px) {
                .auth-glass {
                    grid-template-columns: 1fr;
                }

                .auth-hero {
                    border-right: none;
                    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
                    padding: 22px 20px;
                    gap: 6px;
                }

                .auth-title {
                    font-size: 2.1rem;
                    line-height: 1.2;
                }

                .auth-sub {
                    font-size: 0.86rem;
                    line-height: 1.5;
                    margin-bottom: 0;
                }

                .auth-hero-art {
                    width: 72px;
                    height: 72px;
                    align-self: flex-start;
                    margin-top: 8px;
                }

                .vault-toast {
                    top: 10px;
                    min-width: 0;
                    max-width: 94vw;
                    font-size: 0.82rem;
                    padding: 10px 12px;
                    border-radius: 12px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    function injectAuthHtml() {
        const gate = document.createElement('div');
        gate.id = 'auth-gate';
        gate.innerHTML = `
            <div class="auth-glass">
                <div class="auth-hero">
                    <h2 class="auth-title text-3xl mb-3">Your Personal Anime &amp; Waifu Sanctuary.</h2>
                    <p class="auth-sub mb-4">Simpan list favorit anime dan waifu menjadi satu dan di mana saja.</p>
                    <div class="auth-kicker">Selalu ada untukmu, bahkan saat dunia sedang offline 🌸</div>
                    <img class="auth-hero-art" src="icons/icon.png" alt="Vault mascot" loading="lazy">
                </div>
                <div class="auth-forms">
                    <div class="auth-tab-row">
                        <button class="auth-tab active" id="auth-tab-login">Login</button>
                        <button class="auth-tab" id="auth-tab-signup">Signup</button>
                    </div>

                    <form id="form-login">
                        <input class="auth-input" name="identifier" placeholder="Email atau Username" required>
                        <div class="auth-input-wrap">
                            <input class="auth-input" id="login-password" name="password" type="password" placeholder="Password" required>
                            <button class="auth-eye" type="button" data-toggle-password="login-password" aria-label="Tampilkan password"></button>
                        </div>
                        <button class="auth-btn auth-btn-main" type="submit">Masuk</button>
                        <button class="auth-btn auth-btn-alt mt-2" type="button" id="btn-biometric-login">Login dengan Biometri/PIN</button>
                        <button class="auth-btn auth-btn-ghost mt-2" type="button" id="btn-open-forgot">Lupa Password</button>
                    </form>

                    <form id="form-signup" class="hidden-auth">
                        <input class="auth-input" name="username" placeholder="Username" required>
                        <input class="auth-input" name="email" type="email" placeholder="Email" required>
                        <div class="auth-input-wrap">
                            <input class="auth-input" id="signup-password" name="password" type="password" placeholder="Password (min 8 char)" required>
                            <button class="auth-eye" type="button" data-toggle-password="signup-password" aria-label="Tampilkan password"></button>
                        </div>
                        <button class="auth-btn auth-btn-main" type="submit">Buat Akun</button>
                    </form>

                    <form id="form-forgot" class="hidden-auth">
                        <input class="auth-input" name="email" type="email" placeholder="Email akun" required>
                        <button class="auth-btn auth-btn-main" type="submit">Generate Token Reset</button>
                        <p id="forgot-token-display" class="text-xs text-pink-200/90 mt-2"></p>
                        <input class="auth-input mt-3" name="token" placeholder="Token reset">
                        <div class="auth-input-wrap">
                            <input class="auth-input" id="forgot-new-password" name="new_password" type="password" placeholder="Password baru (min 8 char)">
                            <button class="auth-eye" type="button" data-toggle-password="forgot-new-password" aria-label="Tampilkan password"></button>
                        </div>
                        <button class="auth-btn auth-btn-alt" type="button" id="btn-reset-password">Reset Password</button>
                        <button class="auth-btn auth-btn-ghost mt-2" type="button" id="btn-back-login">Kembali ke Login</button>
                    </form>

                    <div class="auth-message" id="auth-message"></div>
                </div>
            </div>
        `;
        document.body.appendChild(gate);
        state.authGateEl = gate;

        const toast = document.createElement('div');
        toast.id = 'vault-global-toast';
        toast.className = 'vault-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        document.body.appendChild(toast);
        state.toastEl = toast;

        const profileModal = document.createElement('div');
        profileModal.id = 'profile-settings-modal';
        profileModal.className = 'modal-overlay';
        profileModal.innerHTML = `
            <div class="modal-box max-w-lg" onclick="event.stopPropagation()">
                <h3 class="text-xl font-bold text-purple-200 mb-4" style="font-family: 'Nunito', sans-serif;">Pengaturan Profil</h3>
                <div class="flex gap-4 items-center mb-4">
                    <img id="profile-modal-preview" src="" alt="profile" class="w-20 h-20 rounded-full border border-white/20 object-cover cursor-pointer" onclick="if(this.src && !this.src.includes('dicebear')) openLightbox(this.src)">
                    <div class="flex-1"> 
                        <input id="profile-photo-input" type="file" accept="image/*" class="hidden">
                        <button id="btn-choose-profile-photo" class="btn-edit w-full py-2 text-xs font-bold">Edit Foto Profil</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-xs text-purple-200">Username</label>
                    <input id="profile-username-input" type="text" class="glass-input mt-1" placeholder="Username">
                </div>

                <div class="mb-3">
                    <label class="text-xs text-purple-200">Bio</label>
                    <textarea id="profile-bio-input" class="glass-input mt-1" rows="3" placeholder="Tulis bio kamu... (maks 10 kata)"></textarea>
                </div>
                <button id="btn-save-profile" class="btn-primary w-full py-2.5 rounded-xl text-sm">Simpan Profil</button>

                <hr class="my-4 border-white/10">

                <button id="btn-toggle-password-form" class="btn-edit w-full py-2.5 rounded-xl text-sm mb-2">Ubah Password</button>
                <div id="password-change-section" class="hidden overflow-hidden transition-all duration-300 ease-out max-h-0 opacity-0 -translate-y-1">
                    <div class="mb-3">
                        <label class="text-xs text-purple-200">Password Lama</label>
                        <input id="profile-current-password" type="password" class="glass-input mt-1" placeholder="Password lama">
                    </div>
                    <div class="mb-3">
                        <label class="text-xs text-purple-200">Password Baru</label>
                        <input id="profile-new-password" type="password" class="glass-input mt-1" placeholder="Password baru">
                    </div>
                    <button id="btn-reveal-profile-password" class="btn-edit w-full py-2 rounded-xl text-xs mb-2">Lihat Password (Biometri/PIN)</button>
                    <p id="profile-password-countdown" class="text-[11px] text-purple-200/80 -mt-1 mb-2 hidden"></p>
                    <button id="btn-change-password" class="btn-edit w-full py-2.5 rounded-xl text-sm mb-2">Ganti Password</button>
                </div>

                <hr class="my-4 border-white/10">

                <button id="btn-enable-biometric" class="btn-primary w-full py-2.5 rounded-xl text-sm mb-2">Aktifkan Biometri/PIN</button>
                <p class="text-xs text-purple-200/80 mb-2">Dukung PIN Laptop (Windows Hello) atau sensor Biometri HP.</p>
                <button id="btn-logout" class="btn-danger w-full py-2.5 rounded-xl text-sm">Logout</button>
                <p id="profile-settings-message" class="text-xs mt-2 text-purple-200/80"></p>
            </div>
        `;
        profileModal.addEventListener('click', (e) => {
            if (e.target.id === 'profile-settings-modal') {
                profileModal.classList.remove('open');
            }
        });
        document.body.appendChild(profileModal);
        state.profileModalEl = profileModal;

        const cropModal = document.createElement('div');
        cropModal.id = 'profile-crop-modal';
        cropModal.className = 'modal-overlay';
        cropModal.innerHTML = `
            <div class="modal-box max-w-2xl" onclick="event.stopPropagation()">
                <h3 class="text-lg font-bold text-white mb-4">Edit Foto Profil</h3>
                <div class="bg-black/40 rounded-xl overflow-hidden mb-4">
                    <img id="profile-crop-image" src="" alt="crop" style="max-width:100%;display:block;">
                </div>
                <div class="flex gap-2">
                    <button id="btn-cancel-profile-crop" class="flex-1 py-2 rounded-lg border border-white/10 text-gray-300">Batal</button>
                    <button id="btn-apply-profile-crop" class="flex-1 btn-primary py-2 rounded-lg">Gunakan Foto</button>
                </div>
            </div>
        `;
        cropModal.addEventListener('click', (e) => {
            if (e.target.id === 'profile-crop-modal') {
                closeProfileCrop();
            }
        });
        document.body.appendChild(cropModal);
    }

    function placeProfileButton() {
        const brandRow = document.getElementById('navbar-brand-row');
        const titleEl = document.getElementById('navbar-title');
        const btn = document.getElementById('nav-profile-btn');
        if (!brandRow || !titleEl || !btn) return;
        if (btn.nextElementSibling !== titleEl) {
            brandRow.insertBefore(btn, titleEl);
        }
    }

    function ensureNavbarProfileButton() {
        const brandRow = document.getElementById('navbar-brand-row');
        if (!brandRow) return;

        const legacyLogo = document.getElementById('navbar-logo');
        if (legacyLogo) legacyLogo.remove();

        let btn = document.getElementById('nav-profile-btn');
        if (!btn) {
            btn = document.createElement('button');
            btn.id = 'nav-profile-btn';
            btn.className = 'profile-nav-btn';
            btn.title = 'Profil';
            btn.innerHTML = '<img id="nav-profile-img" src="' + avatarFallback('vault-user') + '" alt="profile">';
            btn.addEventListener('click', () => openProfileModal());
            brandRow.prepend(btn);
        }

        placeProfileButton();
    }

    function setAuthMessage(msg, ok = false) {
        const el = document.getElementById('auth-message');
        if (!el) return;
        el.style.color = ok ? '#86efac' : '#fca5a5';
        el.textContent = msg || '';
    }

    function showGlobalToast(message) {
        if (!state.toastEl || !message) return;
        state.toastEl.textContent = message;
        state.toastEl.classList.add('open');

        if (state.toastHideTimer) {
            clearTimeout(state.toastHideTimer);
        }
        state.toastHideTimer = setTimeout(() => {
            if (state.toastEl) {
                state.toastEl.classList.remove('open');
            }
        }, 4200);
    }

    function setProfileMessage(msg, ok = false) {
        const el = document.getElementById('profile-settings-message');
        if (!el) return;
        el.style.color = ok ? '#86efac' : '#fca5a5';
        el.textContent = msg || '';
    }

    function setForgotTokenMessage(msg) {
        const el = document.getElementById('forgot-token-display');
        if (!el) return;
        el.textContent = msg || '';
    }

    function updateRevealButtonLabel() {
        const revealBtn = document.getElementById('btn-reveal-profile-password');
        if (!revealBtn) return;

        if (state.profilePasswordsVisible) {
            revealBtn.textContent = 'Sembunyikan Password';
            return;
        }

        if (Date.now() < state.profilePasswordUnlockedUntil) {
            revealBtn.textContent = 'Lihat Password (Akses 30 detik aktif)';
            return;
        }

        revealBtn.textContent = 'Lihat Password (Biometri/PIN)';
    }

    function setProfilePasswordInputsVisible(isVisible) {
        const current = document.getElementById('profile-current-password');
        const next = document.getElementById('profile-new-password');

        if (current) current.type = isVisible ? 'text' : 'password';
        if (next) next.type = isVisible ? 'text' : 'password';

        state.profilePasswordsVisible = isVisible;
        updateRevealButtonLabel();
    }

    function updateProfilePasswordCountdownLabel() {
        const el = document.getElementById('profile-password-countdown');
        if (!el) return;

        const remainingMs = state.profilePasswordUnlockedUntil - Date.now();
        if (remainingMs <= 0) {
            el.textContent = '';
            el.classList.add('hidden');
            return;
        }

        const remainingSec = Math.ceil(remainingMs / 1000);
        el.textContent = 'Akses lihat password tersisa ' + remainingSec + ' detik';
        el.classList.remove('hidden');
    }

    function startProfilePasswordCountdown() {
        if (state.profilePasswordCountdownTimer) {
            clearInterval(state.profilePasswordCountdownTimer);
            state.profilePasswordCountdownTimer = null;
        }

        updateProfilePasswordCountdownLabel();
        if (Date.now() >= state.profilePasswordUnlockedUntil) {
            return;
        }

        state.profilePasswordCountdownTimer = setInterval(() => {
            if (Date.now() >= state.profilePasswordUnlockedUntil) {
                if (state.profilePasswordCountdownTimer) {
                    clearInterval(state.profilePasswordCountdownTimer);
                    state.profilePasswordCountdownTimer = null;
                }
            }
            updateProfilePasswordCountdownLabel();
        }, 250);
    }

    function clearProfilePasswordRevealTimer() {
        if (state.profilePasswordRevealTimer) {
            clearTimeout(state.profilePasswordRevealTimer);
            state.profilePasswordRevealTimer = null;
        }
        if (state.profilePasswordCountdownTimer) {
            clearInterval(state.profilePasswordCountdownTimer);
            state.profilePasswordCountdownTimer = null;
        }
        state.profilePasswordUnlockedUntil = 0;
        updateRevealButtonLabel();
        updateProfilePasswordCountdownLabel();
    }

    async function beginProfilePasswordPressReveal() {
        if (state.profilePasswordVerifying) return;

        if (state.profilePasswordsVisible) {
            setProfilePasswordInputsVisible(false);
            return;
        }

        if (!state.hasBiometric) {
            setProfileMessage('Aktifkan Biometri/PIN terlebih dahulu untuk melihat password.');
            return;
        }

        if (Date.now() >= state.profilePasswordUnlockedUntil) {
            state.profilePasswordVerifying = true;
            const verified = await verifyBiometricForSensitiveAction();
            state.profilePasswordVerifying = false;
            if (!verified) {
                setProfileMessage('Verifikasi Biometri/PIN diperlukan untuk melihat password.');
                return;
            }

            state.profilePasswordUnlockedUntil = Date.now() + 30000;
            if (state.profilePasswordRevealTimer) {
                clearTimeout(state.profilePasswordRevealTimer);
            }
            state.profilePasswordRevealTimer = setTimeout(() => {
                state.profilePasswordUnlockedUntil = 0;
                state.profilePasswordRevealTimer = null;
                setProfilePasswordInputsVisible(false);
                updateProfilePasswordCountdownLabel();
            }, 30000);
            setProfileMessage('Akses lihat password aktif 30 detik.', true);
            startProfilePasswordCountdown();
            updateRevealButtonLabel();

            setProfilePasswordInputsVisible(true);
            return;
        }

        setProfilePasswordInputsVisible(true);
    }

    function lockAppContent() {
        if (state.appMainEl) {
            state.appMainEl.classList.add('opacity-0', 'pointer-events-none');
            state.appMainEl.setAttribute('aria-hidden', 'true');
        }
    }

    function unlockAppContent() {
        if (state.appMainEl) {
            state.appMainEl.classList.remove('opacity-0', 'pointer-events-none');
            state.appMainEl.removeAttribute('aria-hidden');
        }
    }

    function hideInitLoader() {
        if (!state.appInitLoaderEl) return;
        state.appInitLoaderEl.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(() => {
            if (state.appInitLoaderEl) state.appInitLoaderEl.classList.add('hidden');
        }, 350);
    }

    function showAuthGate(force = false, customMessage = '') {
        if (!state.authGateEl) return;
        state.authGateEl.classList.add('open');
        lockAppContent();
        document.body.classList.add('auth-locked');
        if (customMessage) {
            setAuthMessage(customMessage);
        } else if (force) {
            setAuthMessage('Session habis. Silakan login lagi.');
        }
    }

    function hideAuthGate() {
        if (!state.authGateEl) return;
        state.authGateEl.classList.remove('open');
        unlockAppContent();
        document.body.classList.remove('auth-locked');
        setAuthMessage('');
    }

    function switchAuthTab(tab) {
        const loginTab = document.getElementById('auth-tab-login');
        const signupTab = document.getElementById('auth-tab-signup');
        const loginForm = document.getElementById('form-login');
        const signupForm = document.getElementById('form-signup');
        const forgotForm = document.getElementById('form-forgot');

        loginTab.classList.toggle('active', tab === 'login');
        signupTab.classList.toggle('active', tab === 'signup');

        loginForm.classList.toggle('hidden-auth', tab !== 'login');
        signupForm.classList.toggle('hidden-auth', tab !== 'signup');
        forgotForm.classList.add('hidden-auth');
        setForgotTokenMessage('');
        setAuthMessage('');
    }

    function updateProfileUi() {
        const img = document.getElementById('nav-profile-img');
        const modalImg = document.getElementById('profile-modal-preview');
        const usernameInput = document.getElementById('profile-username-input');
        const bioInput = document.getElementById('profile-bio-input');
        const biometricBtn = document.getElementById('btn-enable-biometric');
        const dashboardUsername = document.getElementById('dashboard-user-name') || document.getElementById('dashboard-username');

        const profileImg = (state.user && state.user.profile_pict) || avatarFallback(state.user ? state.user.username : 'vault-user');

        if (img) img.src = profileImg;
        if (modalImg) modalImg.src = profileImg;
        if (usernameInput) usernameInput.value = (state.user && state.user.username) || '';
        if (bioInput) bioInput.value = (state.user && state.user.bio) || '';
        if (biometricBtn) biometricBtn.textContent = state.hasBiometric ? 'Perbarui Biometri/PIN' : 'Aktifkan Biometri/PIN';
        if (dashboardUsername) {
            dashboardUsername.textContent = (state.user && state.user.username) ? state.user.username : 'Guest';
        }
    }

    async function refreshSession() {
        try {
            const res = await nativeFetch('api.php?action=session_status', {
                credentials: 'same-origin',
                cache: 'no-store'
            });
            const data = await res.json();
            if (data && data.authenticated) {
                state.user = data.user;
                state.hasBiometric = !!data.has_biometric;
                updateProfileUi();
                hideAuthGate();
                window.dispatchEvent(new CustomEvent('vault-authenticated'));
            } else {
                state.user = null;
                state.hasBiometric = false;
                updateProfileUi();
                const reason = data && data.reason ? String(data.reason) : '';
                if (reason === 'password_changed') {
                    const msg = (data && data.message) ? data.message : 'Password akun telah diganti. Silakan login ulang.';
                    showAuthGate(false, msg);
                    showGlobalToast(msg);
                } else {
                    showAuthGate();
                }
            }
        } catch (e) {
            state.user = null;
            state.hasBiometric = false;
            updateProfileUi();
            showAuthGate();
        } finally {
            hideInitLoader();
        }
    }

    async function handleLoginSubmit(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const res = await fetch('api.php?action=login', { method: 'POST', body: fd });
        const data = await res.json();
        if (!data.success) {
            setAuthMessage(data.message || 'Login gagal');
            return;
        }
        setAuthMessage('Login berhasil', true);
        await refreshSession();
    }

    async function handleSignupSubmit(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const res = await fetch('api.php?action=signup', { method: 'POST', body: fd });
        const data = await res.json();
        if (!data.success) {
            setAuthMessage(data.message || 'Signup gagal');
            return;
        }
        setAuthMessage('Akun berhasil dibuat', true);
        await refreshSession();
    }

    async function handleForgotSubmit(e) {
        e.preventDefault();
        const fd = new FormData(e.target);
        const res = await fetch('api.php?action=forgot_password', { method: 'POST', body: fd });
        const data = await res.json();
        if (data && data.reset_token) {
            const tokenInput = e.target.querySelector('input[name="token"]');
            if (tokenInput) tokenInput.value = String(data.reset_token).toUpperCase();
            setForgotTokenMessage('Token Reset Kamu: ' + data.reset_token);
        } else {
            setForgotTokenMessage('');
        }
        setAuthMessage(data.message || 'Cek email/token reset', true);
    }

    async function handleResetPassword() {
        const form = document.getElementById('form-forgot');
        const fd = new FormData();
        const rawToken = form.querySelector('input[name="token"]').value || '';
        const cleanToken = String(rawToken).toUpperCase().replace(/[^A-Z0-9]/g, '').trim();
        form.querySelector('input[name="token"]').value = cleanToken;
        fd.append('token', cleanToken);
        fd.append('new_password', form.querySelector('input[name="new_password"]').value || '');
        const res = await fetch('api.php?action=reset_password', { method: 'POST', body: fd });
        const data = await res.json();
        setAuthMessage(data.message || (data.success ? 'Reset berhasil' : 'Reset gagal'), !!data.success);
    }

    function initPasswordVisibilityToggles() {
        function getEyeIconSvg(isVisible) {
            if (isVisible) {
                // Open eye icon
                return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
            // Closed eye icon
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.92 10.92 0 0 1 12 19c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.06-5.94"></path><path d="M9.9 4.24A10.84 10.84 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-3.22 4.62"></path><path d="M1 1l22 22"></path></svg>';
        }

        function syncEyeButton(btn, input) {
            const isVisible = input.type === 'text';
            btn.innerHTML = getEyeIconSvg(isVisible);
            btn.setAttribute('aria-label', isVisible ? 'Sembunyikan password' : 'Tampilkan password');
        }

        document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const inputId = btn.getAttribute('data-toggle-password');
                const input = inputId ? document.getElementById(inputId) : null;
                if (!input) return;

                const nextType = input.type === 'password' ? 'text' : 'password';
                input.type = nextType;
                syncEyeButton(btn, input);
            });

            const inputId = btn.getAttribute('data-toggle-password');
            const input = inputId ? document.getElementById(inputId) : null;
            if (input) syncEyeButton(btn, input);
        });
    }

    function strToBuf(str) {
        return new TextEncoder().encode(str);
    }

    function bufToB64url(buf) {
        const bytes = new Uint8Array(buf);
        let binary = '';
        for (let i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

    function b64urlToBuf(base64url) {
        const pad = '='.repeat((4 - (base64url.length % 4)) % 4);
        const base64 = (base64url + pad).replace(/-/g, '+').replace(/_/g, '/');
        const binary = atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes;
    }

    function getWebAuthnUnavailableReason() {
        if (window.isSecureContext === false) {
            return 'WebAuthn butuh secure context (HTTPS).';
        }
        if (!window.PublicKeyCredential) {
            return 'PublicKeyCredential API tidak tersedia di browser ini.';
        }
        return 'WebAuthn tidak tersedia karena kebijakan browser/perangkat.';
    }

    async function doBiometricLogin() {
        if (!window.PublicKeyCredential) {
            const reason = getWebAuthnUnavailableReason();
            console.error('[WebAuthn] doBiometricLogin unavailable:', {
                reason,
                isSecureContext: window.isSecureContext,
                hostname: window.location.hostname,
                origin: window.location.origin
            });
            setAuthMessage('Perangkat/browser tidak mendukung WebAuthn. Buka via HTTPS.');
            return;
        }

        try {
            const identifierEl = document.querySelector('#form-login input[name="identifier"]');
            const email = String(identifierEl ? identifierEl.value : '').trim().toLowerCase();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                setAuthMessage('Untuk login biometrik, isi Email akun yang valid dulu.');
                return;
            }

            const beginRes = await fetch('api.php?action=webauthn_begin_login&email=' + encodeURIComponent(email));
            const begin = await beginRes.json();
            if (!begin.success) {
                setAuthMessage(begin.message || 'Gagal memulai biometric login');
                return;
            }

            const allowCredentials = Array.isArray(begin.allow_credentials)
                ? begin.allow_credentials
                    .map((item) => {
                        if (!item || !item.id) return null;
                        return {
                            type: 'public-key',
                            id: b64urlToBuf(String(item.id)),
                            transports: Array.isArray(item.transports) ? item.transports : undefined
                        };
                    })
                    .filter(Boolean)
                : [];

            const assertion = await navigator.credentials.get({
                publicKey: {
                    challenge: b64urlToBuf(begin.challenge),
                    // preferred allows Windows/Mac to offer device PIN fallback.
                    userVerification: 'preferred',
                    rpId: WEBAUTHN_RP_ID,
                    ...(allowCredentials.length ? { allowCredentials } : {})
                }
            });

            if (!assertion) {
                setAuthMessage('Biometric login dibatalkan');
                return;
            }

            const payload = {
                credentialId: assertion.id,
                challenge: begin.challenge
            };

            const finishRes = await fetch('api.php?action=webauthn_finish_login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const finish = await finishRes.json();
            if (!finish.success) {
                setAuthMessage(finish.message || 'Biometric login gagal');
                return;
            }

            setAuthMessage('Login biometrik berhasil', true);
            await refreshSession();
        } catch (e) {
            console.error('[WebAuthn] doBiometricLogin runtime failure:', {
                error: e,
                isSecureContext: window.isSecureContext,
                hostname: window.location.hostname,
                origin: window.location.origin,
                rpId: WEBAUTHN_RP_ID
            });
            setAuthMessage('Gagal memproses WebAuthn. Cek console untuk detail.');
        }
    }

    async function enableBiometric() {
        if (!window.PublicKeyCredential) {
            const reason = getWebAuthnUnavailableReason();
            console.error('[WebAuthn] enableBiometric unavailable:', {
                reason,
                isSecureContext: window.isSecureContext,
                hostname: window.location.hostname,
                origin: window.location.origin
            });
            setProfileMessage('Perangkat/browser tidak mendukung WebAuthn. Buka via HTTPS.');
            return;
        }

        try {
            const beginRes = await fetch('api.php?action=webauthn_begin_register');
            const begin = await beginRes.json();
            if (!begin.success) {
                setProfileMessage(begin.message || 'Gagal memulai registrasi biometrik');
                return;
            }

            const userIdBytes = strToBuf(begin.user.id);
            const credential = await navigator.credentials.create({
                publicKey: {
                    challenge: b64urlToBuf(begin.challenge),
                    rp: {
                        name: 'Anime & Waifu Vault',
                        id: WEBAUTHN_RP_ID
                    },
                    user: {
                        id: userIdBytes,
                        name: begin.user.name,
                        displayName: begin.user.displayName
                    },
                    pubKeyCredParams: [
                        { type: 'public-key', alg: -7 },
                        { type: 'public-key', alg: -257 }
                    ],
                    authenticatorSelection: {
                        // preferred allows device PIN when biometric is unavailable.
                        userVerification: 'preferred',
                        residentKey: 'preferred'
                    },
                    timeout: 60000,
                    attestation: 'none'
                }
            });

            if (!credential) {
                setProfileMessage('Registrasi biometrik dibatalkan');
                return;
            }

            const transports = credential.response.getTransports ? credential.response.getTransports() : [];
            const publicKey = credential.response.getPublicKey ? bufToB64url(credential.response.getPublicKey()) : '';

            const finishRes = await fetch('api.php?action=webauthn_finish_register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    challenge: begin.challenge,
                    credentialId: credential.id,
                    publicKey,
                    signCount: 0,
                    transports
                })
            });

            const finish = await finishRes.json();
            if (!finish.success) {
                setProfileMessage(finish.message || 'Gagal menyimpan credential biometrik');
                return;
            }

            state.hasBiometric = true;
            updateProfileUi();
            setProfileMessage('Biometri/PIN berhasil diaktifkan', true);
        } catch (e) {
            console.error('[WebAuthn] enableBiometric runtime failure:', {
                error: e,
                isSecureContext: window.isSecureContext,
                hostname: window.location.hostname,
                origin: window.location.origin,
                rpId: WEBAUTHN_RP_ID
            });
            setProfileMessage('Gagal registrasi WebAuthn. Cek console untuk detail.');
        }
    }

    function openProfileModal() {
        if (!state.user) {
            showAuthGate();
            return;
        }
        setProfileMessage('');
        setPasswordSectionVisible(false);
        const toggleBtn = document.getElementById('btn-toggle-password-form');
        if (toggleBtn) toggleBtn.textContent = 'Ubah Password';
        const currentPassEl = document.getElementById('profile-current-password');
        const newPassEl = document.getElementById('profile-new-password');
        if (currentPassEl) currentPassEl.value = '';
        if (newPassEl) newPassEl.value = '';
        clearProfilePasswordRevealTimer();
        setProfilePasswordInputsVisible(false);
        updateRevealButtonLabel();
        updateProfileUi();
        state.profileModalEl.classList.add('open');
    }

    function setPasswordSectionVisible(show) {
        const section = document.getElementById('password-change-section');
        if (!section) return;

        if (show) {
            section.classList.remove('hidden');
            requestAnimationFrame(() => {
                section.classList.remove('max-h-0', 'opacity-0', '-translate-y-1');
                section.classList.add('max-h-64', 'opacity-100', 'translate-y-0');
            });
            return;
        }

        clearProfilePasswordRevealTimer();
        setProfilePasswordInputsVisible(false);
        section.classList.remove('max-h-64', 'opacity-100', 'translate-y-0');
        section.classList.add('max-h-0', 'opacity-0', '-translate-y-1');
        setTimeout(() => {
            if (!section.classList.contains('max-h-64')) {
                section.classList.add('hidden');
            }
        }, 300);
    }

    async function saveProfile() {
        const bioValue = document.getElementById('profile-bio-input').value || '';
        if (countWords(bioValue) > 10) {
            setProfileMessage('Bio maksimal 10 kata.');
            return;
        }

        const fd = new FormData();
        fd.append('username', document.getElementById('profile-username-input').value || '');
        fd.append('bio', bioValue);
        if (state.pendingProfileDataUrl) {
            fd.append('profile_pict_data_url', state.pendingProfileDataUrl);
        }

        const res = await fetch('api.php?action=update_profile', { method: 'POST', body: fd });
        const data = await res.json();
        if (!data.success) {
            setProfileMessage(data.message || 'Gagal update profil');
            return;
        }

        state.user = data.user;
        state.pendingProfileDataUrl = '';
        updateProfileUi();
        setProfileMessage('Profil berhasil disimpan', true);
    }

    async function changePassword() {
        const currentPassword = document.getElementById('profile-current-password').value || '';
        const newPassword = document.getElementById('profile-new-password').value || '';

        if (!currentPassword || !newPassword) {
            setProfileMessage('Isi password lama dan password baru terlebih dahulu.');
            return;
        }

        if (!state.hasBiometric) {
            setProfileMessage('Aktifkan Biometri/PIN terlebih dahulu sebelum ganti password.');
            return;
        }

        const verified = await verifyBiometricForSensitiveAction();
        if (!verified) {
            setProfileMessage('Verifikasi Biometri/PIN diperlukan untuk ganti password.');
            return;
        }

        const fd = new FormData();
        fd.append('current_password', currentPassword);
        fd.append('new_password', newPassword);

        const res = await fetch('api.php?action=change_password', { method: 'POST', body: fd });
        const data = await res.json();
        setProfileMessage(data.message || (data.success ? 'Password berhasil diganti' : 'Gagal ganti password'), !!data.success);

        if (data.success) {
            document.getElementById('profile-current-password').value = '';
            document.getElementById('profile-new-password').value = '';
            clearProfilePasswordRevealTimer();
            setProfilePasswordInputsVisible(false);
            setPasswordSectionVisible(false);
            const toggleBtn = document.getElementById('btn-toggle-password-form');
            if (toggleBtn) toggleBtn.textContent = 'Ubah Password';
        }
    }

    async function verifyBiometricForSensitiveAction() {
        if (!window.PublicKeyCredential) return false;

        try {
            const beginRes = await fetch('api.php?action=webauthn_begin_login');
            const begin = await beginRes.json();
            if (!begin.success) return false;

            const assertion = await navigator.credentials.get({
                publicKey: {
                    challenge: b64urlToBuf(begin.challenge),
                    // preferred enables PIN fallback on Windows Hello/macOS.
                    userVerification: 'preferred',
                    rpId: WEBAUTHN_RP_ID
                }
            });

            if (!assertion) return false;

            const finishRes = await fetch('api.php?action=webauthn_finish_login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    credentialId: assertion.id,
                    challenge: begin.challenge
                })
            });
            const finish = await finishRes.json();
            return !!finish.success;
        } catch (e) {
            console.error('[WebAuthn] verifyBiometricForSensitiveAction failed:', e);
            return false;
        }
    }

    async function doLogout() {
        await fetch('api.php?action=logout');
        document.querySelectorAll('form').forEach(form => form.reset());
        clearProfilePasswordRevealTimer();
        setProfilePasswordInputsVisible(false);
        state.user = null;
        state.hasBiometric = false;
        updateProfileUi();
        if (state.profileModalEl) state.profileModalEl.classList.remove('open');
        showAuthGate();
    }

    function startProfileCrop(file) {
        if (!window.Cropper) {
            setProfileMessage('Cropper.js tidak tersedia, upload langsung dipakai.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const cropImg = document.getElementById('profile-crop-image');
            cropImg.src = e.target.result;
            document.getElementById('profile-crop-modal').classList.add('open');

            if (state.cropper) state.cropper.destroy();
            state.cropper = new Cropper(cropImg, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                background: false
            });
        };
        reader.readAsDataURL(file);
    }

    function closeProfileCrop() {
        document.getElementById('profile-crop-modal').classList.remove('open');
        if (state.cropper) {
            state.cropper.destroy();
            state.cropper = null;
        }
    }

    function applyProfileCrop() {
        if (!state.cropper) return;

        const canvas = state.cropper.getCroppedCanvas({ width: 512, height: 512 });
        state.pendingProfileDataUrl = canvas.toDataURL('image/png');
        const modalImg = document.getElementById('profile-modal-preview');
        modalImg.src = state.pendingProfileDataUrl;
        closeProfileCrop();
        setProfileMessage('Foto siap disimpan. Klik "Simpan Profil". ', true);
    }

    function bindEvents() {
        document.getElementById('auth-tab-login').addEventListener('click', () => switchAuthTab('login'));
        document.getElementById('auth-tab-signup').addEventListener('click', () => switchAuthTab('signup'));

        document.getElementById('form-login').addEventListener('submit', handleLoginSubmit);
        document.getElementById('form-signup').addEventListener('submit', handleSignupSubmit);
        document.getElementById('form-forgot').addEventListener('submit', handleForgotSubmit);

        document.getElementById('btn-open-forgot').addEventListener('click', () => {
            document.getElementById('form-login').classList.add('hidden-auth');
            document.getElementById('form-signup').classList.add('hidden-auth');
            document.getElementById('form-forgot').classList.remove('hidden-auth');
            setForgotTokenMessage('');
            setAuthMessage('');
        });

        document.getElementById('btn-toggle-password-form').addEventListener('click', () => {
            const section = document.getElementById('password-change-section');
            if (!section) return;
            const nextOpen = section.classList.contains('hidden');
            setPasswordSectionVisible(nextOpen);
            const toggleBtn = document.getElementById('btn-toggle-password-form');
            if (toggleBtn) toggleBtn.textContent = nextOpen ? 'Tutup Form Password' : 'Ubah Password';
            setProfileMessage('');
        });

        document.getElementById('btn-back-login').addEventListener('click', () => switchAuthTab('login'));
        document.getElementById('btn-reset-password').addEventListener('click', handleResetPassword);
        document.getElementById('btn-biometric-login').addEventListener('click', doBiometricLogin);

        document.getElementById('btn-save-profile').addEventListener('click', saveProfile);
        document.getElementById('btn-change-password').addEventListener('click', changePassword);
        const revealBtn = document.getElementById('btn-reveal-profile-password');
        if (revealBtn) {
            revealBtn.addEventListener('click', (e) => {
                e.preventDefault();
                beginProfilePasswordPressReveal();
            });
        }
        document.getElementById('btn-enable-biometric').addEventListener('click', enableBiometric);
        document.getElementById('btn-logout').addEventListener('click', doLogout);

        document.getElementById('btn-choose-profile-photo').addEventListener('click', () => {
            document.getElementById('profile-photo-input').click();
        });

        document.getElementById('profile-photo-input').addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;
            startProfileCrop(file);
        });

        document.getElementById('btn-cancel-profile-crop').addEventListener('click', closeProfileCrop);
        document.getElementById('btn-apply-profile-crop').addEventListener('click', applyProfileCrop);

        window.addEventListener('vault-open-profile', openProfileModal);
        window.addEventListener('vault-force-session-refresh', () => {
            refreshSession();
        });
    }

    async function init() {
        addStyles();
        injectAuthHtml();
        state.appMainEl = document.getElementById('app-main');
        state.appInitLoaderEl = document.getElementById('app-init-loader');
        lockAppContent();
        ensureNavbarProfileButton();
        await refreshSession();
        initPasswordVisibilityToggles();
        bindEvents();

        window.addEventListener('resize', placeProfileButton);
    }

    document.addEventListener('DOMContentLoaded', init);
})();
