document.addEventListener('DOMContentLoaded', function() {
            // Function to open modal
            function openImageModal(imageSrc, title) {
                const modalContent = `
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">${title}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="${imageSrc}" class="img-fluid" alt="${title}">
                                </div>
                            </div>
                        </div>
                    </div>`;

                // Remove existing modal if any
                const existingModal = document.getElementById('imageModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Add new modal to body
                document.body.insertAdjacentHTML('beforeend', modalContent);

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('imageModal'));
                modal.show();

                // Remove modal from DOM after it's hidden
                document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            }

            // Add click handlers to all proof images
            document.querySelectorAll('.proof-image').forEach(img => {
                img.addEventListener('click', function() {
                    openImageModal(this.src, this.getAttribute('data-title'));
                });
            });
        });