// CSS
import '../../styles/pages/snowtrick-edit.css';

// JS
import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
	// Initialize media handling
	initializeMediaHandling();

	// Setup delete trick functionality
	setupDeleteTrick();
});

function initializeMediaHandling() {
	// Open add media modal
	const addMediaButton = document.querySelector('.add-media-card');
	if (addMediaButton) {
		addMediaButton.addEventListener('click', function() {
			const modal = new bootstrap.Modal(document.getElementById('addMediaModal'));
			modal.show();
		});
	}

	// Toggle media type in add media modal
	const mediaTypeSelect = document.getElementById('mediaType');
	if (mediaTypeSelect) {
		mediaTypeSelect.addEventListener('change', function() {
			toggleMediaForms(this.value);
		});
	}

	// Setup edit media buttons
	setupEditMediaButtons();

	// Handle add media confirmation
	setupAddMediaButton();

	// Handle delete media buttons
	setupDeleteMediaButtons();
}

function toggleMediaForms(mediaType) {
	const imageForm = document.getElementById('imageUploadForm');
	const videoForm = document.getElementById('videoUrlForm');

	if (mediaType === 'image') {
		imageForm.style.display = 'block';
		videoForm.style.display = 'none';
	} else {
		imageForm.style.display = 'none';
		videoForm.style.display = 'block';
	}
}

function setupEditMediaButtons() {
	document.querySelectorAll('.btn-edit-media').forEach(button => {
		button.addEventListener('click', function() {
			const fileId = this.getAttribute('data-file-id');
			const fileType = this.getAttribute('data-file-type');

			// Set values in the edit modal
			document.getElementById('editFileId').value = fileId;
			document.getElementById('editMediaType').value = fileType;

			// Toggle appropriate form
			if (fileType === 'image') {
				document.getElementById('editImageForm').style.display = 'block';
				document.getElementById('editVideoForm').style.display = 'none';
			} else {
				document.getElementById('editImageForm').style.display = 'none';
				document.getElementById('editVideoForm').style.display = 'block';
			}
		});
	});
}

function setupAddMediaButton() {
	const confirmAddMediaBtn = document.getElementById('confirmAddMedia');
	if (confirmAddMediaBtn) {
		confirmAddMediaBtn.addEventListener('click', function() {
			const mediaType = document.getElementById('mediaType').value;
			let tempPreview;
			let formData = new FormData();

			formData.append('mediaType', mediaType);

			if (mediaType === 'image') {
				const imageFile = document.getElementById('newImageFile').files[0];
				if (!imageFile) {
					alert('Please select an image file');
					return;
				}
				formData.append('imageFile', imageFile);
				tempPreview = URL.createObjectURL(imageFile);
			} else {
				const videoUrl = document.getElementById('newVideoUrl').value;
				if (!videoUrl) {
					alert('Please enter a video URL');
					return;
				}
				formData.append('videoUrl', videoUrl);
				tempPreview = videoUrl;
			}

			// Add media to carousel (most recent first)
			addMediaToCarousel(mediaType, tempPreview);

			// Add to hidden form field to be processed on form submission
			addMediaToForm(mediaType, mediaType === 'image' ? document.getElementById('newImageFile').files[0] : tempPreview);

			// Close modal and reset form
			bootstrap.Modal.getInstance(document.getElementById('addMediaModal')).hide();
			document.getElementById('newImageFile').value = '';
			document.getElementById('newVideoUrl').value = '';
		});
	}
}

function addMediaToCarousel(type, content) {
	const mediaCarousel = document.querySelector('.media-carousel');
	const addButton = document.querySelector('.add-media-card');

	const mediaItem = document.createElement('div');
	mediaItem.className = `media-item media-item-${type} temp-media`;

	if (type === 'image') {
		mediaItem.innerHTML = `
            <img src="${content}" alt="New media" class="img-fluid">
            <div class="media-actions">
                <button type="button" class="btn btn-remove-temp">
                    <i class="fas fa-times text-danger"></i>
                </button>
            </div>
        `;
	} else {
		mediaItem.innerHTML = `
            <div class="ratio ratio-16x9 h-100">
                <iframe src="${content}" allowfullscreen></iframe>
            </div>
            <div class="media-actions">
                <button type="button" class="btn btn-remove-temp">
                    <i class="fas fa-times text-danger"></i>
                </button>
            </div>
        `;
	}

	// Insert right after the add button (newest first)
	mediaCarousel.insertBefore(mediaItem, addButton.nextSibling);

	// Add event listener to remove button
	mediaItem.querySelector('.btn-remove-temp').addEventListener('click', function() {
		// Also remove the corresponding form input
		const tempMediaIndex = Array.from(mediaCarousel.querySelectorAll('.temp-media')).indexOf(mediaItem);
		const mediaInputs = document.querySelectorAll('input[name="new_media[]"]');
		if (mediaInputs[tempMediaIndex]) {
			mediaInputs[tempMediaIndex].remove();
		}
		mediaItem.remove();
	});
}

function addMediaToForm(type, content) {
	// Create a unique ID for this media
	const tempId = 'temp_' + Date.now();

	// Add to hidden form field to be processed on form submission
	const mediaDataInput = document.createElement('input');
	mediaDataInput.type = 'hidden';
	mediaDataInput.name = 'new_media[]';
	mediaDataInput.value = JSON.stringify({
		id: tempId,
		type: type,
		content: type === 'image' ? content.name : content
	});

	document.getElementById('snowtrickForm').appendChild(mediaDataInput);

	// For images, create a file input to hold the actual file data
	if (type === 'image') {
		const fileInput = document.createElement('input');
		fileInput.type = 'file';
		fileInput.name = 'imageFile_' + tempId;
		fileInput.style.display = 'none';

		// Create a FileList-like object with our file
		const dataTransfer = new DataTransfer();
		dataTransfer.items.add(content);
		fileInput.files = dataTransfer.files;

		document.getElementById('snowtrickForm').appendChild(fileInput);
	}
}

function setupDeleteMediaButtons() {
	document.querySelectorAll('.btn-delete-media').forEach(button => {
		button.addEventListener('click', function() {
			document.getElementById('deleteFileId').value = this.getAttribute('data-file-id');
		});
	});
}

function setupDeleteTrick() {
	const confirmDeleteTrickBtn = document.getElementById('confirmDeleteTrick');
	if (confirmDeleteTrickBtn) {
		confirmDeleteTrickBtn.addEventListener('click', function() {
			const trickId = this.getAttribute('data-trick-id');

			fetch(`/snowtrick/delete/${trickId}`, {
				method: 'DELETE',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(() => {
				// Redirect to homepage after successful deletion
				window.location.href = '/';
			})
			.catch(error => {
				console.error('Error:', error);
				alert('An error occurred while deleting the trick.');
			});
		});
	}
}
