const setupLoadMoreBtn = () => {
	const btn = document.querySelector('.btn-load-more');
	if (!btn) return;
	handleEvent(btn);
};

const fetchTricks = async (page) => {
	const response = await fetch('/snowtrick/load-more/{page}'.replace('{page}', page));
	return response.json();
};

const handleEvent = (btn) => {
	btn.onclick = async () => {
		const page = btn.dataset.page;
		if (!page) return;
		const res = await fetchTricks(page);
		if (!res.success) return;

		const tricks = res.data.tricks;
		if (!tricks || tricks.length === 0) {
			btn.remove();
			return;
		}

		const tricksContainer = document.querySelector('.tricks-container');
		if (!tricksContainer) return;

		let html = '';
		tricks.forEach((trick) => {
			html += `
            <div class="col">
                <div class="card h-100 shadow trick-card">
                    <div class="card-img-wrapper overflow-hidden">
                        <img src="https://picsum.photos/300"
                            class="card-img-top trick-image"
                            alt="${trick.name}">
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <h3 class="card-title">${trick.name}</h3>
                        ${res.data.isCurrentUserLoggedIn ? `
                            <div class="action-buttons">
                                <button class="btn btn-outline-success btn-edit-trick" data-trick-id="${trick.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-delete-trick" data-trick-id="${trick.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            `;
		});

		tricksContainer.innerHTML += html;
		if (!res.data.isThereAnyTricksLeftToDisplay) {
			btn.remove();
			return;
		}

		const pageInt = parseInt(page);
		btn.setAttribute('data-page', pageInt + 1);
		handleEvent(btn);
	};
};

document.addEventListener('DOMContentLoaded', setupLoadMoreBtn);
