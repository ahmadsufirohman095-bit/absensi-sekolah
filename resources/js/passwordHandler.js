export function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    const eyeOpen = document.getElementById('eye-open-' + id);
    const eyeClosed = document.getElementById('eye-closed-' + id);

    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        if (eyeOpen) eyeOpen.classList.add('hidden');
        if (eyeClosed) eyeClosed.classList.remove('hidden');
    } else {
        input.type = 'password';
        if (eyeOpen) eyeOpen.classList.remove('hidden');
        if (eyeClosed) eyeClosed.classList.add('hidden');
    }
}

export function initializePasswordStrengthChecker(passwordInputId, passwordStrengthId) {
    const passwordInput = document.getElementById(passwordInputId);
    const passwordStrengthContainer = document.getElementById(passwordStrengthId);

    if (!passwordInput || !passwordStrengthContainer) {
        return;
    }

    passwordStrengthContainer.className = 'mt-2';
    passwordStrengthContainer.innerHTML = `
        <div class="flex items-center justify-between">
            <div id="password-strength-segments-${passwordInputId}" class="flex-grow flex items-center space-x-1">
                <div class="password-strength-segment"></div>
                <div class="password-strength-segment"></div>
                <div class="password-strength-segment"></div>
                <div class="password-strength-segment"></div>
                <div class="password-strength-segment"></div>
            </div>
            <span id="password-strength-text-${passwordInputId}" class="text-xs font-medium w-28 text-right ml-2"></span>
        </div>
    `;

    // Inject styles for the segments to avoid needing separate CSS changes
    const styleId = 'password-strength-segment-styles';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .password-strength-segment {
                height: 5px;
                flex-grow: 1;
                border-radius: 3px;
                background-color: #e5e7eb; /* bg-gray-200 */
                transition: background-color 0.3s ease-in-out;
            }
            .dark .password-strength-segment {
                background-color: #374151; /* dark:bg-gray-700 */
            }
        `;
        document.head.appendChild(style);
    }

    const segments = passwordStrengthContainer.querySelectorAll('.password-strength-segment');
    const strengthText = document.getElementById(`password-strength-text-${passwordInputId}`);

    if (segments.length !== 5 || !strengthText) return;

    const originalBorderClasses = passwordInput.className.split(' ').filter(c => c.startsWith('border-') || c.startsWith('focus:border-') || c.startsWith('focus:ring-'));

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        let message = '';
        let activeColor = '';
        let textColor = 'text-gray-500';
        let borderColorClasses = [];

        if (password.length > 0) {
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/V/.test(password)) strength++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;
            if (password.length >= 12) strength++;
        }

        switch (strength) {
            case 0: message = ''; activeColor = '#e5e7eb'; textColor = 'text-gray-500 dark:text-gray-400'; break;
            case 1: message = 'Sangat Lemah'; activeColor = '#ef4444'; textColor = 'text-red-500'; borderColorClasses = ['border-red-500', 'dark:border-red-500', 'focus:border-red-500', 'dark:focus:border-red-500', 'focus:ring-red-500', 'dark:focus:ring-red-500']; break;
            case 2: message = 'Lemah'; activeColor = '#f97316'; textColor = 'text-orange-500'; borderColorClasses = ['border-orange-500', 'dark:border-orange-500', 'focus:border-orange-500', 'dark:focus:border-orange-500', 'focus:ring-orange-500', 'dark:focus:ring-orange-500']; break;
            case 3: message = 'Sedang'; activeColor = '#eab308'; textColor = 'text-yellow-500'; borderColorClasses = ['border-yellow-500', 'dark:border-yellow-500', 'focus:border-yellow-500', 'dark:focus:border-yellow-500', 'focus:ring-yellow-500', 'dark:focus:ring-yellow-500']; break;
            case 4: message = 'Kuat'; activeColor = '#84cc16'; textColor = 'text-lime-500'; borderColorClasses = ['border-lime-500', 'dark:border-lime-500', 'focus:border-lime-500', 'dark:focus:border-lime-500', 'focus:ring-lime-500', 'dark:focus:ring-lime-500']; break;
            case 5: message = 'Sangat Kuat'; activeColor = '#22c55e'; textColor = 'text-green-500'; borderColorClasses = ['border-green-500', 'dark:border-green-500', 'focus:border-green-500', 'dark:focus:border-green-500', 'focus:ring-green-500', 'dark:focus:ring-green-500']; break;
        }

        const darkSegmentColor = '#374151';
        const lightSegmentColor = '#e5e7eb';
        const isDark = document.documentElement.classList.contains('dark');

        segments.forEach((segment, index) => {
            if (index < strength) {
                segment.style.backgroundColor = activeColor;
            } else {
                segment.style.backgroundColor = isDark ? darkSegmentColor : lightSegmentColor;
            }
        });

        if (password.length === 0) {
            message = '';
            borderColorClasses = originalBorderClasses;
        }

        strengthText.textContent = message;
        strengthText.className = `text-xs font-medium w-28 text-right ml-2 ${textColor}`;

        const currentClasses = passwordInput.className.split(' ');
        const classesToRemove = currentClasses.filter(c => c.startsWith('border-') || c.startsWith('focus:border-') || c.startsWith('focus:ring-'));
        if (classesToRemove.length > 0) {
            passwordInput.classList.remove(...classesToRemove);
        }
        
        if (borderColorClasses.length > 0) {
            passwordInput.classList.add(...borderColorClasses);
        } else {
            passwordInput.classList.add(...originalBorderClasses);
        }
    });

    passwordInput.form.addEventListener('reset', () => {
        const isDark = document.documentElement.classList.contains('dark');
        const segmentColor = isDark ? '#374151' : '#e5e7eb';
        segments.forEach(segment => {
            segment.style.backgroundColor = segmentColor;
        });
        strengthText.textContent = '';
        strengthText.className = 'text-xs font-medium w-28 text-right ml-2 text-gray-500 dark:text-gray-400';
        
        const currentClasses = passwordInput.className.split(' ');
        const classesToRemove = currentClasses.filter(c => c.startsWith('border-') || c.startsWith('focus:border-') || c.startsWith('focus:ring-'));
        if (classesToRemove.length > 0) {
            passwordInput.classList.remove(...classesToRemove);
        }
        passwordInput.classList.add(...originalBorderClasses);
    });
}