class IconPicker {
    constructor(jsonUrl) {
        this.jsonUrl = jsonUrl;
        this.iconsData = null;
        this.selectedInput = null;
        this.modalInitialized = false;
        this.init();
    }

    init() {
        document.addEventListener("click", (event) => {
            if (event.target.classList.contains("iconPicker")) {
                this.selectedInput = event.target;
                this.showModal();
            }
        });
    }

    async loadIcons() {
        if (!this.iconsData) {
            try {
                const response = await fetch(this.jsonUrl);
                this.iconsData = await response.json();
            } catch (error) {
                console.error("Error loading icons:", error);
            }
        }
        this.renderIcons();
    }

    createModal() {
        if (this.modalInitialized) return;

        this.modalInitialized = true;
        this.modal = document.createElement("div");
        this.modal.className = "modal fade";
        this.modal.id = "iconPickerModal";
        this.modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Select an Icon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="iconSearch" class="form-control mb-3" placeholder="Search icons...">
                        <div id="iconList" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(this.modal);

        // Event Listeners
        document.getElementById("iconSearch").addEventListener("keyup", (e) => {
            this.renderIcons(e.target.value.toLowerCase());
        });

        this.modal.addEventListener("click", (event) => {
            let target = event.target;

            // If clicking on the <i> tag inside .icon-item, get the parent
            if (target.tagName === "I") {
                target = target.parentElement; // Move up to the .icon-item div
            }

            if (target.classList.contains("icon-item")) {
                const iconClass = target.dataset.icon;

                if (this.selectedInput) {
                    this.selectedInput.value = iconClass;

                    // Find the nearest .iconPreview element and update it
                    // Find the nearest .iconPreview (similar to .parent().find(".iconPreview") in jQuery)
                    const previewHolder = this.selectedInput.closest(".iconPreview") || // Check parent chain
                        this.selectedInput.parentElement.querySelector(".iconPreview"); // Find inside parent

                    if (previewHolder) {
                        previewHolder.innerHTML = `<i class="${iconClass} fa-2x"></i>`;
                    }
                }

                bootstrap.Modal.getInstance(this.modal).hide();
            }
        });



        this.bsModal = new bootstrap.Modal(this.modal);
    }

    renderIcons(filter = "") {
        const iconContainer = document.getElementById("iconList");
        iconContainer.innerHTML = "";
        for (const key in this.iconsData) {
            const iconClass = this.iconsData[key];
            if (iconClass.includes(filter)) {
                const iconElement = document.createElement("div");
                iconElement.className = "icon-item text-center p-2";
                iconElement.style.cursor = "pointer";
                iconElement.dataset.icon = iconClass;
                iconElement.innerHTML = `<i class="${iconClass} fa-2x"></i>`;
                iconContainer.appendChild(iconElement);
            }
        }
    }

    async showModal() {
        this.createModal();
        await this.loadIcons();
        this.bsModal.show();
    }
}

const iconPicker = new IconPicker(base_url + "mtplPlugins/iconPicker/iconSet.json");