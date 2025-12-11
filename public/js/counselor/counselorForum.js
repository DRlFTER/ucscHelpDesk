// counselorForum.js

document.addEventListener('DOMContentLoaded', function() {
    initializeForumControls();
    initializeNewTopicModal();
});

/**
 * Initialize forum search and filter controls
 */
function initializeForumControls() {
    const searchInput = document.getElementById('searchForum');
    const categoryFilter = document.getElementById('filterCategory');
    const sortBySelect = document.getElementById('sortBy');
    
    // Debounce search
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 500);
        });
    }
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', performSearch);
    }
    
    if (sortBySelect) {
        sortBySelect.addEventListener('change', performSearch);
    }
}

/**
 * Perform search/filter operation
 */
function performSearch() {
    const query = document.getElementById('searchForum').value.trim();
    const category = document.getElementById('filterCategory').value;
    const sortBy = document.getElementById('sortBy').value;
    
    const params = new URLSearchParams();
    if (query) params.append('q', query);
    if (category) params.append('category', category);
    if (sortBy) params.append('sort', sortBy);
    
    fetch(`/counselor/forumSearch?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTopicsList(data.topics);
            } else {
                console.error('Search failed:', data.message);
            }
        })
        .catch(error => {
            console.error('Error performing search:', error);
        });
}

/**
 * Update topics list in DOM
 */
function updateTopicsList(topics) {
    const forumTopics = document.getElementById('forumTopics');
    
    if (!topics || topics.length === 0) {
        forumTopics.innerHTML = `
            <div class="empty" style="text-align: center; padding: 3rem 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; opacity: 0.3; color: #9ca3af;">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <p style="margin: 0; font-size: 1.1rem; color: #6b7280;">No discussions found.</p>
            </div>
        `;
        return;
    }
    
    let html = '<ul class="itemList" style="gap: 0;">';
    
    topics.forEach(topic => {
        const excerpt = topic.content.length > 150 
            ? topic.content.substring(0, 150) + '...' 
            : topic.content;
        
        const pinnedBadge = topic.is_pinned 
            ? '<span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem;">📌 Pinned</span>' 
            : '';
        
        html += `
            <li class="item forum-item" style="cursor: pointer; border-left: 3px solid #667eea;" onclick="viewTopic(${topic.id})">
                <div style="display: flex; gap: 1rem; align-items: start; width: 100%;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; flex-shrink: 0;">
                        ${topic.author_name.charAt(0).toUpperCase()}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: start; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #1a1a1a;">
                                ${escapeHtml(topic.title)}
                            </h4>
                            ${pinnedBadge}
                        </div>
                        <p style="margin: 0 0 0.75rem 0; color: #666; font-size: 0.9rem; line-height: 1.5;">
                            ${escapeHtml(excerpt)}
                        </p>
                        <div class="itemMeta">
                            <span class="badge" style="background: #e0e7ff; color: #3730a3;">
                                ${formatCategory(topic.category)}
                            </span>
                            <span class="dot">•</span>
                            <span>by <strong>${escapeHtml(topic.author_name)}</strong></span>
                            <span class="dot">•</span>
                            <span>${formatDate(topic.created_at)}</span>
                            <span class="dot">•</span>
                            <span>💬 ${topic.reply_count} replies</span>
                            <span class="dot">•</span>
                            <span>👁 ${topic.view_count} views</span>
                        </div>
                    </div>
                </div>
            </li>
        `;
    });
    
    html += '</ul>';
    forumTopics.innerHTML = html;
}

/**
 * View topic details
 */
function viewTopic(topicId) {
    window.location.href = `/counselor/forumTopic?id=${topicId}`;
}

/**
 * Initialize new topic modal
 */
function initializeNewTopicModal() {
    const form = document.getElementById('newTopicForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            createNewTopic();
        });
    }
}

/**
 * Open new topic modal
 */
function openNewTopicModal() {
    const modal = document.getElementById('newTopicModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close new topic modal
 */
function closeNewTopicModal() {
    const modal = document.getElementById('newTopicModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Reset form
        document.getElementById('newTopicForm').reset();
    }
}

/**
 * Create new topic
 */
function createNewTopic() {
    const title = document.getElementById('topicTitle').value.trim();
    const category = document.getElementById('topicCategory').value;
    const content = document.getElementById('topicContent').value.trim();
    const isPinned = document.getElementById('topicPinned').checked;
    
    if (!title || !category || !content) {
        alert('Please fill in all required fields');
        return;
    }
    
    const data = {
        title: title,
        category: category,
        content: content,
        is_pinned: isPinned
    };
    
    fetch('/counselor/forumCreateTopic', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeNewTopicModal();
            // Redirect to new topic
            window.location.href = `/counselor/forumTopic?id=${data.topic_id}`;
        } else {
            alert('Error creating topic: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to create topic. Please try again.');
    });
}

/**
 * Utility: Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Utility: Format category
 */
function formatCategory(category) {
    return category.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

/**
 * Utility: Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('newTopicModal');
    if (modal && e.target === modal) {
        closeNewTopicModal();
    }
});