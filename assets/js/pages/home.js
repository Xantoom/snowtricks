// CSS
import '../../styles/pages/home.css';

import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
	// Scroll to top button functionality
	const scrollTopBtn = document.querySelector('.scroll-top-btn');
	if (scrollTopBtn) {
		// Show button when user scrolls down 300px
		window.addEventListener('scroll', function() {
			if (window.scrollY > 300) {
				scrollTopBtn.classList.remove('d-none');
				scrollTopBtn.classList.add('visible');
			} else {
				scrollTopBtn.classList.remove('visible');
				// Don't immediately hide it - add a small delay for animation
				setTimeout(() => {
					if (!scrollTopBtn.classList.contains('visible')) {
						scrollTopBtn.classList.add('d-none');
					}
				}, 300);
			}
		});

		// Smooth scroll to top when clicked
		scrollTopBtn.addEventListener('click', function(e) {
			e.preventDefault();
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});
	}

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

	// Setup for delete buttons using event delegation
	const deleteModal = new bootstrap.Modal(document.getElementById('deleteTrickModal'));
	const confirmDeleteButton = document.getElementById('confirmDeleteTrick');
	let trickIdToDelete = null;

	// Event delegation for delete buttons
	document.addEventListener('click', function(e) {
		if (e.target.classList.contains('btn-delete-trick') ||
			e.target.closest('.btn-delete-trick')) {
			const button = e.target.classList.contains('btn-delete-trick') ?
				e.target : e.target.closest('.btn-delete-trick');
			trickIdToDelete = button.getAttribute('data-trick-id');
			deleteModal.show();
		}
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
			window.location.reload();
		})
		.catch(error => {
			console.error('Error:', error);
			deleteModal.hide();
			sessionStorage.setItem('flashMessage', 'Error deleting trick: ' + error.message ? error.message : 'Unknown error');
			window.location.reload();
		});
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
