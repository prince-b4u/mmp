for (const btn of document.querySelectorAll('.filter-btn')) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const cat = this.getAttribute('data-category');
        for (const b of document.querySelectorAll('.filter-btn')) {
            b.classList.remove('btn-primary');
        }
        this.classList.add('btn-primary');
        for (const card of document.querySelectorAll('.listing-card')) {
            if (cat === 'all' || card.getAttribute('data-category') === cat) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        }
    });
}

for (const form of document.querySelectorAll('.add-to-cart-form')) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const listingId = this.dataset.listingId;
        const quantityInput = this.querySelector('input[name="quantity"]');
        const quantity = quantityInput ? Number.parseInt(quantityInput.value, 10) : 1;
        fetch('/cart_add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `listing_id=${encodeURIComponent(listingId)}&quantity=${encodeURIComponent(quantity)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    let badge = document.getElementById('cart-badge');
                    if (!badge) {
                        const span = document.createElement('span');
                        span.id = 'cart-badge';
                        span.className = 'absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5';
                        badge = span;
                        const cartBtn = document.querySelector('a[href="/cart.php"] .relative');
                        if (cartBtn) {
                            cartBtn.appendChild(badge);
                        }
                    }
                    badge.textContent = data.cart_count;
                    badge.style.display = data.cart_count > 0 ? '' : 'none';
                } else {
                    alert(data.message || 'Failed to add to cart.');
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                alert('Error adding to cart.');
            });
    });
}

for (const card of document.querySelectorAll('.listing-card')) {
    card.addEventListener('click', function(e) {
        if (e.target.closest('.add-to-cart-form')) return;

        const listing = JSON.parse(this.dataset.listing);
        const images = this.dataset.images ? JSON.parse(this.dataset.images) : [];

        let imagesHtml = '';
        if (images.length) {
            imagesHtml = `<div class="grid grid-cols-2 gap-2 mb-4">${images.map(img =>
                    `<div>
                        <img src="/${img}" class="w-full object-cover h-40 rounded" alt="Listing image" />
                    </div>`
                ).join('')}</div>`;
        } else {
            imagesHtml = `<div class="w-full h-48 flex items-center justify-center bg-gray-100 text-gray-500 mb-4">No Images</div>`;
        }

        let modalContent = `
            <h2 class="text-2xl font-bold mb-2">${listing.title}</h2>
            ${imagesHtml}
            <p class="text-green-600 font-bold mb-2">R${Number(listing.price).toFixed(2)}</p>
            <p class="mb-1"><strong>Location:</strong> ${listing.location ?? 'Unknown location'}</p>
            <p class="mb-1"><strong>Seller:</strong> ${listing.username}</p>
            <p class="mb-1"><strong>Quantity:</strong> ${listing.quantity}</p>
            <p class="mb-4">${listing.description ?? ''}</p>
        `;

        if (window.isLoggedIn) {
            modalContent += `
            <form id="modal-add-to-cart-form" class="flex gap-2 items-center mb-4">
                <input type="number" name="quantity" min="1" max="${listing.quantity}" value="1"
                    class="input input-bordered input-xs h-10 w-20" required>
                <button type="submit" class="bg-accent text-white px-3 py-1 rounded hover:bg-accent-focus flex items-center gap-2">
                    <span class="icon-[material-symbols--shopping-cart] text-2xl"></span>
                    Add to Cart
                </button>
            </form>
            `;
        }

        modalContent += `
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Close</button>
                </form>
            </div>
        `;

        document.getElementById('listing-modal-content').innerHTML = modalContent;
        document.getElementById('listing-modal').showModal();

        if (window.isLoggedIn) {
            const modalForm = document.getElementById('modal-add-to-cart-form');
            if (modalForm) {
                modalForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const quantity = modalForm.querySelector('input[name="quantity"]').value;
                    fetch('/cart_add.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `listing_id=${encodeURIComponent(listing.id)}&quantity=${encodeURIComponent(quantity)}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                let badge = document.getElementById('cart-badge');
                                if (!badge) {
                                    const span = document.createElement('span');
                                    span.id = 'cart-badge';
                                    span.className = 'absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-2 py-0.5';
                                    badge = span;
                                    const cartBtn = document.querySelector('a[href="/cart.php"] .relative');
                                    if (cartBtn) {
                                        cartBtn.appendChild(badge);
                                    }
                                }
                                badge.textContent = data.cart_count;
                                badge.style.display = data.cart_count > 0 ? '' : 'none';
                                document.getElementById('listing-modal').close();
                            } else {
                                alert(data.message || 'Failed to add to cart.');
                            }
                        })
                        .catch(err => {
                            console.error('Fetch error:', err);
                            alert('Error adding to cart.');
                        });
                });
            }
        }
    });
}