/**
 * 自動目次生成スクリプト
 */

document.addEventListener('DOMContentLoaded', function() {
    const postContent = document.querySelector('.post-content');
    const tocNav = document.getElementById('toc-nav');
    
    // h2の直前のhrを非表示にする
    if (postContent) {
        const h2Elements = postContent.querySelectorAll('h2');
        h2Elements.forEach(function(h2) {
            const previousElement = h2.previousElementSibling;
            if (previousElement && previousElement.tagName === 'HR') {
                previousElement.style.display = 'none';
            }
        });
    }
    
    if (!postContent || !tocNav) {
        return;
    }
    
    // 見出しを取得（h2, h3）
    const headings = postContent.querySelectorAll('h2[id], h3[id], h2, h3');
    
    if (headings.length === 0) {
        // id属性がない場合は自動生成
        const allHeadings = postContent.querySelectorAll('h2, h3');
        allHeadings.forEach(function(heading, index) {
            if (!heading.id) {
                // idを自動生成
                let id = heading.textContent.trim()
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\u3040-\u309F\u30A0-\u30FF\u4E00-\u9FAF-]+/g, '');
                
                // 既存のidと重複しないように
                let uniqueId = id;
                let counter = 1;
                while (document.getElementById(uniqueId)) {
                    uniqueId = id + '-' + counter;
                    counter++;
                }
                
                heading.id = uniqueId;
            }
        });
        
        // 再度取得
        const updatedHeadings = postContent.querySelectorAll('h2[id], h3[id]');
        generateTOC(updatedHeadings, tocNav);
    } else {
        generateTOC(headings, tocNav);
    }
    
    // スクロール時のアクティブ表示
    updateActiveTOCItem();
    window.addEventListener('scroll', updateActiveTOCItem);
});

// 「人気の投稿」ウィジェットを目立たせる
document.addEventListener('DOMContentLoaded', function() {
    const sidebarBelow = document.querySelector('.sidebar-below');
    if (sidebarBelow) {
        const widgets = sidebarBelow.querySelectorAll('.widget');
        widgets.forEach(function(widget) {
            const title = widget.querySelector('.widget-title');
            if (title && title.textContent.trim() === '人気の投稿') {
                widget.classList.add('highlight-popular');
            }
            // カテゴリーウィジェットにクラスを追加（既にwidget_categoriesクラスがある場合は不要）
            if (widget.classList.contains('widget_categories')) {
                widget.classList.add('widget-category-below');
            }
        });
    }
});

/**
 * 目次を生成
 */
function generateTOC(headings, tocNav) {
    if (headings.length === 0) {
        document.getElementById('table-of-contents').style.display = 'none';
        return;
    }
    
    let tocHTML = '<ul class="toc-list">';
    let currentLevel = 0;
    
    headings.forEach(function(heading) {
        const level = parseInt(heading.tagName.charAt(1)); // h2 -> 2, h3 -> 3
        const id = heading.id || heading.textContent.trim().toLowerCase().replace(/\s+/g, '-');
        const text = heading.textContent.trim();
        
        if (!heading.id) {
            heading.id = id;
        }
        
        if (level > currentLevel) {
            // サブリストを開始
            tocHTML += '<ul class="toc-sublist">';
        } else if (level < currentLevel) {
            // サブリストを閉じる
            for (let i = level; i < currentLevel; i++) {
                tocHTML += '</ul>';
            }
        }
        
        tocHTML += '<li class="toc-item toc-level-' + level + '">';
        tocHTML += '<a href="#' + id + '" class="toc-link">' + escapeHtml(text) + '</a>';
        tocHTML += '</li>';
        
        currentLevel = level;
    });
    
    // 残りのリストを閉じる
    while (currentLevel > 2) {
        tocHTML += '</ul>';
        currentLevel--;
    }
    
    tocHTML += '</ul>';
    tocNav.innerHTML = tocHTML;
    
    // スムーズスクロール
    tocNav.querySelectorAll('a.toc-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // ヘッダー分のオフセット
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
                
                // URLを更新（履歴に追加しない）
                history.pushState(null, null, '#' + targetId);
            }
        });
    });
}

/**
 * 現在のセクションをハイライト
 */
function updateActiveTOCItem() {
    const headings = document.querySelectorAll('.post-content h2[id], .post-content h3[id]');
    const tocLinks = document.querySelectorAll('.toc-link');
    
    if (headings.length === 0 || tocLinks.length === 0) {
        return;
    }
    
    // 現在のスクロール位置を取得
    const scrollPos = window.scrollY + 100;
    
    let currentHeading = null;
    
    headings.forEach(function(heading) {
        const top = heading.offsetTop;
        if (scrollPos >= top) {
            currentHeading = heading;
        }
    });
    
    // すべてのリンクからactiveクラスを削除
    tocLinks.forEach(function(link) {
        link.classList.remove('active');
    });
    
    // 現在の見出しに対応するリンクにactiveクラスを追加
    if (currentHeading) {
        const activeLink = document.querySelector('.toc-link[href="#' + currentHeading.id + '"]');
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
}

/**
 * HTMLエスケープ
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

