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
		            <a href="/snowtrick/${trick.slug}" class="text-decoration-none">
		                <div class="card h-100 shadow trick-card">
		                    <div class="card-img-wrapper overflow-hidden">
		                        <img src="${trick.image.includes('http') ? trick.image : 'uploads/' + trick.image}"
		                            class="card-img-top trick-image"
		                            alt="${trick.name}" />
		                    </div>
		                    <div class="card-body d-flex justify-content-between align-items-center py-3">
		                        <h3 class="card-title text-body">${trick.name}</h3>
		                        ${res.data.isCurrentUserLoggedIn ? `
		                            <div class="action-buttons">
		                                <a class="btn btn-outline-success" href="/snowtrick/edit/${trick.slug}">
		                                    <i class="fas fa-edit"></i>
		                                </a>
		                                <button class="btn btn-outline-danger btn-delete-trick" data-trick-id="${trick.id}">
		                                    <i class="fas fa-trash"></i>
		                                </button>
		                            </div>
		                        ` : ''}
		                    </div>
		                </div>
		            </a>
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
