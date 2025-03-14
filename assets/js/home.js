import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
	// Parallax effect for banner
	window.addEventListener('scroll', function() {
		let scrollPosition = window.scrollY;
		let bannerImage = document.querySelector('.banner-image');
		if (bannerImage) {
			bannerImage.style.transform = 'translateY(' + scrollPosition * 0.3 + 'px)';
		}
	});

	// Smooth scrolling for scroll-down button
	const scrollBtn = document.querySelector('.scroll-down-btn');
	if (scrollBtn) {
		scrollBtn.addEventListener('click', function(e) {
			e.preventDefault();
			document.getElementById('tricks-section').scrollIntoView({
				behavior: 'smooth',
				block: 'start'
			});
		});
	}

	// Setup for delete buttons
	const deleteButtons = document.querySelectorAll('.btn-delete-trick');
	const deleteModal = new bootstrap.Modal(document.getElementById('deleteTrickModal'));
	const confirmDeleteButton = document.getElementById('confirmDeleteTrick');
	let trickIdToDelete = null;

	deleteButtons.forEach(button => {
		button.addEventListener('click', function() {
			trickIdToDelete = this.getAttribute('data-trick-id');
			deleteModal.show();
		});
	});

	confirmDeleteButton.addEventListener('click', function() {
		if (!trickIdToDelete) return;

		fetch(`/snowtrick/delete/${trickIdToDelete}`, {
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
			deleteModal.hide();
			// Add flash message to session storage to display after reload
			sessionStorage.setItem('flashMessage', 'Trick successfully deleted!');
			window.location.reload();
		})
		.catch(error => {
			console.error('Error:', error);
			deleteModal.hide();
			sessionStorage.setItem('flashMessage', 'Error deleting trick');
			window.location.reload();
		});

		// Check for flash message in session storage
		const flashMessage = sessionStorage.getItem('flashMessage');
		if (flashMessage) {
			const alertContainer = document.createElement('div');
			alertContainer.className = 'alert alert-success alert-dismissible fade show';
			alertContainer.setAttribute('role', 'alert');
			alertContainer.innerHTML = `
            ${flashMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

			document.querySelector('.container-fluid').prepend(alertContainer);
			sessionStorage.removeItem('flashMessage');
		}
	});
});
