// CSS
import '../../styles/pages/snowtrick-view.css';

document.addEventListener('DOMContentLoaded', function() {
	// Delete button functionality
	const deleteButton = document.querySelector('.btn-delete-trick');
	const deleteModal = new bootstrap.Modal(document.getElementById('deleteTrickModal'));
	const confirmDeleteButton = document.getElementById('confirmDeleteTrick');

	if (deleteButton) {
		deleteButton.addEventListener('click', function() {
			const trickId = this.getAttribute('data-trick-id');
			confirmDeleteButton.setAttribute('data-trick-id', trickId);
			deleteModal.show();
		});
	}

	if (confirmDeleteButton) {
		confirmDeleteButton.addEventListener('click', function() {
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
				deleteModal.hide();
				sessionStorage.setItem('flashMessage', 'Trick successfully deleted!');
				window.location.href = '/';
			})
			.catch(error => {
				console.error('Error:', error);
				deleteModal.hide();
				alert('Error deleting trick');
			});
		});
	}

	// Load more comments functionality
	const loadMoreBtn = document.getElementById('loadMoreComments');
	if (loadMoreBtn) {
		loadMoreBtn.addEventListener('click', function() {
			const page = parseInt(this.getAttribute('data-page'));
			const trickId = this.getAttribute('data-trick-id');

			fetch(`/comment/load-more/${trickId}/${page}`)
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const commentsContainer = document.querySelector('.comments-container');
					let commentsHtml = '';

					data.data.comments.forEach(comment => {
						commentsHtml += `
                <div class="comment-card card shadow-sm mb-3">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="comment-avatar bg-light rounded-circle me-3 d-flex justify-content-center align-items-center">
                        <i class="fas fa-user text-secondary"></i>
                      </div>
                      <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center">
                          <h6 class="mb-0 fw-bold">${comment.userEmail}</h6>
                          <span class="text-muted small">${comment.createdAt}</span>
                        </div>
                        <p class="mt-2 mb-0">${comment.content}</p>
                      </div>
                    </div>
                  </div>
                </div>
              `;
					});

					commentsContainer.insertAdjacentHTML('beforeend', commentsHtml);

					if (!data.data.hasMoreComments) {
						this.remove();
					} else {
						this.setAttribute('data-page', page + 1);
					}
				}
			})
			.catch(error => console.error('Error:', error));
		});
	}
});
