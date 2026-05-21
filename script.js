document.addEventListener('DOMContentLoaded', () => {
    // 1. Fetch Data from API
    function loadData() {
        fetch('api.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) return console.error(data.error);
                renderPortal(data);
            });
    }

    function renderPortal(data) {
        // Settings
        document.title = data.settings.site_title;
        document.getElementById('site-title').innerText = data.settings.site_title;
        document.getElementById('site-subtitle').innerText = data.settings.site_subtitle;
        document.getElementById('footer-text').innerText = data.settings.footer_text;

        // 1. Render System Cards (Top Apps)
        const cardContainer = document.getElementById('top-cards');
        cardContainer.innerHTML = '';
        
        data.cards.forEach(card => {
            const a = document.createElement('a');
            a.className = 'top-card';
            a.href = card.button_url;
            a.title = card.title;
            a.innerHTML = `
                <div class="top-card-icon">
                    <img src="${card.image_path}" alt="${card.title}" onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(card.title)}&background=random'">
                </div>
                <div class="top-card-label">${card.title}</div>
            `;
            cardContainer.appendChild(a);
        });

        // 2. Render Bookmarks (System + Local)
        const bmContainer = document.getElementById('bookmarks-grid');
        bmContainer.innerHTML = '';

        // Helper to render a bookmark
        const renderBM = (bm, isLocal = false, index = null) => {
            const a = document.createElement('a');
            a.className = 'bookmark-card';
            a.href = bm.url;
            a.target = '_blank';
            
            const icon = bm.icon || (isLocal ? '🔖' : '🔗');
            const deleteBtn = isLocal ? `<button class="bm-delete-btn" onclick="deleteBookmark(event, ${index})" title="Eliminar">&times;</button>` : '';

            a.innerHTML = `
                <div class="bookmark-icon">${icon}</div>
                <div class="bookmark-content">
                    <span class="bookmark-name">${bm.title}</span>
                    <span class="bookmark-url">${bm.url.replace(/^https?:\/\//, '')}</span>
                </div>
                ${deleteBtn}
            `;
            bmContainer.appendChild(a);
        };

        // Render System Bookmarks
        if (data.bookmarks) {
            data.bookmarks.forEach(bm => renderBM(bm, false));
        }

        // Render Local Bookmarks
        const localBookmarks = JSON.parse(localStorage.getItem('my_bookmarks') || '[]');
        localBookmarks.forEach((bm, idx) => renderBM(bm, true, idx));

        // Add a helper for deleting bookmarks
        window.deleteBookmark = (e, index) => {
            e.preventDefault();
            e.stopPropagation();
            if(!confirm("¿Eliminar este marcador?")) return;
            const bms = JSON.parse(localStorage.getItem('my_bookmarks') || '[]');
            bms.splice(index, 1);
            localStorage.setItem('my_bookmarks', JSON.stringify(bms));
            loadData(); // Re-render
        };

        // FAQs
        const faqContainer = document.getElementById('faq-container');
        faqContainer.innerHTML = '';
        data.faqs.forEach(faq => {
            const item = document.createElement('div');
            item.className = 'faq-item';
            item.innerHTML = `
                <div class="faq-q"><span>${faq.question}</span> <span>+</span></div>
                <div class="faq-a">${faq.answer.replace(/\n/g, '<br>')}</div>
            `;
            item.querySelector('.faq-q').onclick = () => {
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
                if (!isActive) item.classList.add('active');
            };
            faqContainer.appendChild(item);
        });

        // Videos
        const videoPlayer = document.getElementById('help-video');
        const videoSource = document.getElementById('help-video-source');
        const btnContainer = document.getElementById('video-buttons');
        btnContainer.innerHTML = '';

        if (data.videos.length > 0) {
            videoSource.src = `video_proxy.php?path=${encodeURIComponent(data.videos[0].source)}`;
            videoPlayer.load();

            data.videos.forEach((v, index) => {
                const btn = document.createElement('button');
                btn.innerText = v.title;
                if (index === 0) btn.className = 'active';
                btn.onclick = () => {
                    document.querySelectorAll('.video-nav button').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    videoSource.src = `video_proxy.php?path=${encodeURIComponent(v.source)}`;
                    videoPlayer.load();
                    videoPlayer.play();
                };
                btnContainer.appendChild(btn);
            });
        }
    }

    // Modal Logic
    window.openBookmarkModal = () => document.getElementById('bookmarkModal').style.display = 'block';
    window.closeBookmarkModal = () => document.getElementById('bookmarkModal').style.display = 'none';

    window.saveBookmark = () => {
        const title = document.getElementById('bm_title').value;
        const url = document.getElementById('bm_url').value;
        if (!title || !url) return alert("Complete los campos");

        const bookmarks = JSON.parse(localStorage.getItem('my_bookmarks') || '[]');
        bookmarks.push({ title, url });
        localStorage.setItem('my_bookmarks', JSON.stringify(bookmarks));
        
        closeBookmarkModal();
        loadData(); // Re-render everything
        
        // Reset form
        document.getElementById('bm_title').value = '';
        document.getElementById('bm_url').value = '';
    };

    loadData();
});
