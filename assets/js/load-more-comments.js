const setupLoadMoreCommentsBtn = () => {
	const btn = document.querySelector('#loadMoreComments');
	if (!btn) return;
	handleEvent(btn);
};

const fetchComments = async (trickId, page) => {
	const response = await fetch(`/comment/load-more/${trickId}/${page}`);
	return response.json();
};

const handleEvent = (btn) => {
	btn.onclick = async () => {
		const page = btn.dataset.page;
		const trickId = btn.dataset.trickId;
		if (!page || !trickId) return;

		const res = await fetchComments(trickId, page);
		if (!res.success) return;

		const comments = res.data.comments;
		if (!comments || comments.length === 0) {
			btn.remove();
			return;
		}

		const commentsContainer = document.querySelector('.comments-container');
		if (!commentsContainer) return;

		let html = '';
		comments.forEach((comment) => {
			html += `
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

		commentsContainer.innerHTML += html;

		if (!res.data.hasMoreComments) {
			btn.remove();
			return;
		}

		const pageInt = parseInt(page);
		btn.setAttribute('data-page', pageInt + 1);
		handleEvent(btn);
	};
};

document.addEventListener('DOMContentLoaded', setupLoadMoreCommentsBtn);
