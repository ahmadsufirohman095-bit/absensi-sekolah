import TomSelect from 'tom-select';

export default () => ({
    tomSelectInstances: [],

    init() {
        // Initial initialization for existing items
        this.initTomSelect(this.$el);

        // Ensure Flatpickr is initialized on load
        this.initFlatpickr(this.$el);
    },

    destroy() {
        this.tomSelectInstances.forEach(instance => {
            if (instance && instance.destroy) {
                instance.destroy();
            }
        });
        this.tomSelectInstances = [];
    },

    initTomSelect(element) {
        // Defer initialization to the next frame to ensure the DOM is fully ready.
        requestAnimationFrame(() => {
            element.querySelectorAll('.jadwal-tom-select').forEach(el => {
                if (!el.tomselect) {
                    const instance = new TomSelect(el, {
                        plugins: ['remove_button'],
                        create: false,
                    });
                    this.tomSelectInstances.push(instance);
                }
            });
        });
    },

    initFlatpickr(element) {
        if (window.flatpickr) {
            element.querySelectorAll('.flatpickr-time').forEach(fp_el => {
                if (!fp_el._flatpickr) { // Check if flatpickr is not already initialized
                    flatpickr(fp_el, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        time_24hr: true,
                    });
                }
            });
        }
    },

    addScheduleItem() {
        const container = this.$refs.container;
        const template = this.$refs.template;
        if (!container || !template) {
            console.error('Container or template ref not found.');
            return;
        }

        const newIndex = Date.now(); // Unique index
        const clone = template.content.cloneNode(true);
        const newItemHtml = clone.firstElementChild.outerHTML.replace(/__INDEX__/g, newIndex);
        
        // Create a new div and append the new item HTML
        const newItemWrapper = document.createElement('div');
        newItemWrapper.innerHTML = newItemHtml;
        const newItem = newItemWrapper.firstElementChild;

        container.appendChild(newItem);

        // Initialize TomSelect and Flatpickr for the new item
        this.initTomSelect(newItem);
        this.initFlatpickr(newItem);
    },

    removeScheduleItem(event) {
        const container = this.$refs.container;
        if (!container) return;

        if (container.children.length > 1) {
            event.target.closest('.schedule-item').remove();
        } else {
            alert('Minimal harus ada satu jadwal.');
        }
    }
});