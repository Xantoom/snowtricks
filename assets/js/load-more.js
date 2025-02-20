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
		console.log(res);
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
				<div class="card h-100 shadow-sm" style="min-width: 250px; max-width: 300px;">
				<img src="https://picsum.photos/300"
					class="card-img-top img-fluid"
					alt="${trick.name}"
					style="height: 200px; object-fit: cover;"
				>
				<div class="card-body d-flex justify-content-between align-items-center">
					<h5 class="card-title">${trick.name}</h5>
					${res.data.isCurrentUserLoggedIn ? `
						<div class="mt-auto">
							<button class="btn btn-success btn-sm btn-edit-trick" data-trick-id="${trick.id}">
								<i class="fas fa-edit"></i>
							</button>
							<button class="btn btn-danger btn-sm btn-delete-trick" data-trick-id="${trick.id}">
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

		btn.setAttribute('data-page', page + 1);
		handleEvent(btn);
	};
};

document.addEventListener('DOMContentLoaded', setupLoadMoreBtn);
