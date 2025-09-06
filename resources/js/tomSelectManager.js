import TomSelect from "tom-select";

/**
 * Alpine.js component to manage multiple TomSelect instances within a container.
 * It automatically initializes on all inner <select> elements
 * and cleans up when the component is removed from the DOM (e.g., by Turbo).
 *
 * Usage:
 * <div x-data="tomSelectManager">
 *   <select>...</select>
 *   <select>...</select>
 * </div>
 */
export default () => ({
    tomSelectInstances: [],

    init() {
        // Find all <select> elements within the component's root element ($el)
        const selectElements = this.$el.querySelectorAll('select');

        if (selectElements.length === 0) {
            return; // No selects to initialize
        }

        selectElements.forEach(el => {
            // Prevent re-initialization
            if (el.tomselect) {
                return;
            }

            // Initialize TomSelect and store the instance
            const instance = new TomSelect(el, {
                plugins: {
                    remove_button: { title: 'Hapus item ini' },
                    clear_button: { title: 'Hapus semua item' },
                    dropdown_input: {},
                },
                create: false,
                copyClassesToDropdown: false,
            });

            // --- JAVASCRIPT HOTFIX for Dark Mode Text Color ---
            // This directly sets the input's text color if dark mode is active,
            // overriding any conflicting styles.
            if (document.documentElement.classList.contains('dark')) {
                instance.control_input.style.color = '#f9fafb'; // Tailwind's text-gray-50
            }
            // We also need to listen for theme changes while the page is open
            new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        if (document.documentElement.classList.contains('dark')) {
                            instance.control_input.style.color = '#f9fafb';
                        } else {
                            instance.control_input.style.color = '#000000'; // Or your default light mode color
                        }
                    }
                });
            }).observe(document.documentElement, { attributes: true });
            // --- END HOTFIX ---

            this.tomSelectInstances.push(instance);
        });
    },

    destroy() {
        // Destroy all TomSelect instances
        this.tomSelectInstances.forEach(instance => {
            if (instance && instance.destroy) {
                instance.destroy();
            }
        });
        this.tomSelectInstances = [];
    }
});
