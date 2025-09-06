
function getTheme() {
    return localStorage.getItem('theme');
}

function isDarkMode() {
    const theme = getTheme();
    return theme === 'dark' || (theme === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
}

function applyTheme(isDark) {
    if (isDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

function setTheme(isDark) {
    applyTheme(isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}



// Export functions to be used in other modules
export { getTheme, isDarkMode, applyTheme, setTheme };
