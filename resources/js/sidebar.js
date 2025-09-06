function getSidebarState() {
    if (localStorage.getItem('sidebarOpen') !== null) {
        return localStorage.getItem('sidebarOpen') === 'true';
    }
    return window.innerWidth >= 1024;
}

function setSidebarState(isOpen) {
    localStorage.setItem('sidebarOpen', isOpen);
}

export { getSidebarState, setSidebarState };
