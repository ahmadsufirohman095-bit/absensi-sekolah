function initTableDragScroll() {
    const container = document.getElementById('attendance-table-container');
    if (!container) return;

    let isDragging = false;
    let startX;
    let scrollLeft;

    // Remove existing listeners to prevent duplicates if called multiple times
    container.removeEventListener('mousedown', handleMouseDown);
    container.removeEventListener('mouseleave', handleMouseLeave);
    container.removeEventListener('mouseup', handleMouseUp);
    container.removeEventListener('mousemove', handleMouseMove);

    function handleMouseDown(e) {
        isDragging = true;
        container.style.cursor = 'grabbing';
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
    }

    function handleMouseLeave() {
        isDragging = false;
        container.style.cursor = 'grab';
    }

    function handleMouseUp() {
        isDragging = false;
        container.style.cursor = 'grab';
    }

    function handleMouseMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 1.5; // Multiplier for faster scroll
        container.scrollLeft = scrollLeft - walk;
    }

    container.addEventListener('mousedown', handleMouseDown);
    container.addEventListener('mouseleave', handleMouseLeave);
    container.addEventListener('mouseup', handleMouseUp);
    container.addEventListener('mousemove', handleMouseMove);

    // Set initial cursor style
    container.style.cursor = 'grab';
}

document.addEventListener('DOMContentLoaded', initTableDragScroll);
window.initTableDragScroll = initTableDragScroll;
