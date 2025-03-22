// CSS
import '../../styles/pages/snowtrick-edit.css';

document.addEventListener('DOMContentLoaded', function() {
	// Toggle media type forms in add media modal
	document.getElementById('mediaType').addEventListener('change', function() {
		const imageForm = document.getElementById('imageUploadForm');
		const videoForm = document.getElementById('videoUrlForm');

		if (this.value === 'image') {
			imageForm.style.display = 'block';
			videoForm.style.display = 'none';
		} else {
			imageForm.style.display = 'none';
			videoForm.style.display = 'block';
		}
	});

	// Edit media modal setup
	const editMediaModal = document.getElementById('editMediaModal');
	if (editMediaModal) {
		editMediaModal.addEventListener('show.bs.modal', function(event) {
			const button = event.relatedTarget;
			const fileId = button.getAttribute('data-file-id');
			const fileType = button.getAttribute('data-file-type');

			document.getElementById('editFileId').value = fileId;

			// Show appropriate form based on file type
			if (fileType === 'image') {
				document.getElementById('editImageForm').style.display = 'block';
				document.getElementById('editVideoForm').style.display = 'none';
			} else {
				document.getElementById('editImageForm').style.display = 'none';
				document.getElementById('editVideoForm').style.display = 'block';
			}
		});
	}

	// Delete media functionality
	const deleteMediaModal = document.getElementById('deleteMediaModal');
	if (deleteMediaModal) {
		deleteMediaModal.addEventListener('show.bs.modal', function(event) {
			const button = event.relatedTarget;
			const fileId = button.getAttribute('data-file-id');
			document.getElementById('confirmDeleteMedia').setAttribute('data-file-id', fileId);
		});
	}

	// Handle delete media confirmation
	const confirmDeleteMediaBtn = document.getElementById('confirmDeleteMedia');
	if (confirmDeleteMediaBtn) {
		confirmDeleteMediaBtn.addEventListener('click', function() {
			const fileId = this.getAttribute('data-file-id');
			// Send delete request
			fetch(`/snowtrick/media/delete/${fileId}`, {
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Hide modal and reload page to reflect changes
					const modal = bootstrap.Modal.getInstance(document.getElementById('deleteMediaModal'));
					modal.hide();
					location.reload();
				}
			})
			.catch(error => console.error('Error:', error));
		});
	}

	// Handle delete banner confirmation
	const confirmDeleteBannerBtn = document.getElementById('confirmDeleteBanner');
	if (confirmDeleteBannerBtn) {
		confirmDeleteBannerBtn.addEventListener('click', function() {
			const fileId = document.querySelector('[data-bs-target="#deleteBannerModal"]').getAttribute('data-file-id');
			// Send delete request
			fetch(`/snowtrick/media/delete/${fileId}`, {
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Hide modal and reload page to reflect changes
					const modal = bootstrap.Modal.getInstance(document.getElementById('deleteBannerModal'));
					modal.hide();
					location.reload();
				}
			})
			.catch(error => console.error('Error:', error));
		});
	}

	// Handle delete trick confirmation
	const confirmDeleteTrickBtn = document.getElementById('confirmDeleteTrick');
	if (confirmDeleteTrickBtn) {
		confirmDeleteTrickBtn.addEventListener('click', function() {
			const trickId = this.getAttribute('data-trick-id');
			// Send delete request
			fetch(`/snowtrick/delete/${trickId}`, {
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(() => {
				// Redirect to homepage after successful deletion
				sessionStorage.setItem('flashMessage', 'Trick successfully deleted!');
				window.location.href = '/';
			})
			.catch(error => console.error('Error:', error));
		});
	}
});
