<main>
  <div class="fullPage">
    <div class="pageLayout">
      <section class="settingsLeft" aria-label="Dashboard navigation">
        <nav class="verticalNav">
          <a class="navItem" href="/counselor/dashboard">Dashboard</a>
          <a class="navItem" href="/counselor/tickets">Counseling Tickets</a>
          <a class="navItem" href="/counselor/calender">Calender</a>
          <a class="navItem active" href="/counselor/forum">Forum</a>
        </nav>
      </section>
      <aside class="adminRight">
        <div class="dashboardContent">
          <div style="margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 700; margin: 0 0 0.5rem 0; color: #111827;">Counseling Forum</h1>
            <p style="color: #6b7280; margin: 0; font-size: 0.95rem;">Professional discussions and student support resources</p>
          </div>

          <!-- Forum Controls -->
          <div class="cardBox" style="margin-bottom: 1rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
              <div style="flex: 1; min-width: 250px;">
                <input 
                  type="text" 
                  id="searchForum" 
                  placeholder="Search discussions..." 
                  style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem;"
                />
              </div>
              <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <select 
                  id="filterCategory" 
                  style="padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem;"
                >
                  <option value="">All Categories</option>
                  <option value="mental_health">Mental Health</option>
                  <option value="academic">Academic Support</option>
                  <option value="career">Career Guidance</option>
                  <option value="personal">Personal Development</option>
                  <option value="resources">Resources</option>
                </select>
                <select 
                  id="sortBy" 
                  style="padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.95rem;"
                >
                  <option value="recent">Most Recent</option>
                  <option value="popular">Most Popular</option>
                  <option value="unanswered">Unanswered</option>
                </select>
                <button 
                  type="button" 
                  class="btnWSvg btnPrimaryText" 
                  onclick="openNewTopicModal()"
                  style="padding: 0.75rem 1.5rem; white-space: nowrap; display: flex; align-items: center; gap: 0.5rem;"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                  </svg>
                  <span>New Topic</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Forum Topics List -->
          <div class="cardBox">
            <div class="cardHeader" style="margin-bottom: 1rem;">
              <h3 style="margin:0;">Forum Discussions</h3>
              <span class="muted">Recent activity</span>
            </div>
            
            <div id="forumTopics">
              <?php if (!empty($forumTopics)): ?>
                <ul class="itemList" style="gap: 0;">
                  <?php foreach ($forumTopics as $topic): ?>
                    <li class="item forum-item" style="cursor: pointer; border-left: 3px solid #667eea;" onclick="viewTopic(<?= (int)$topic['id'] ?>)">
                      <div style="display: flex; gap: 1rem; align-items: start; width: 100%;">
                        <!-- Topic Icon/Avatar -->
                        <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; flex-shrink: 0;">
                          <?= strtoupper(substr($topic['author_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        
                        <!-- Topic Content -->
                        <div style="flex: 1; min-width: 0;">
                          <div style="display: flex; align-items: start; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                            <h4 style="margin: 0; font-size: 1rem; font-weight: 600; color: #1a1a1a;">
                              <?= htmlspecialchars($topic['title'] ?? '') ?>
                            </h4>
                            <?php if (!empty($topic['is_pinned'])): ?>
                              <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem;">📌 Pinned</span>
                            <?php endif; ?>
                          </div>
                          
                          <p style="margin: 0 0 0.75rem 0; color: #666; font-size: 0.9rem; line-height: 1.5;">
                            <?= htmlspecialchars(substr($topic['content'] ?? '', 0, 150)) ?><?= strlen($topic['content'] ?? '') > 150 ? '...' : '' ?>
                          </p>
                          
                          <div class="itemMeta">
                            <span class="badge" style="background: #e0e7ff; color: #3730a3;">
                              <?= ucfirst(str_replace('_', ' ', $topic['category'] ?? 'general')) ?>
                            </span>
                            <span class="dot">•</span>
                            <span>by <strong><?= htmlspecialchars($topic['author_name'] ?? 'Anonymous') ?></strong></span>
                            <span class="dot">•</span>
                            <span><?= isset($topic['created_at']) ? date('M d, Y', strtotime($topic['created_at'])) : '' ?></span>
                            <span class="dot">•</span>
                            <span>💬 <?= (int)($topic['reply_count'] ?? 0) ?> replies</span>
                            <span class="dot">•</span>
                            <span>👁 <?= (int)($topic['view_count'] ?? 0) ?> views</span>
                          </div>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <div class="empty" style="text-align: center; padding: 3rem 1rem;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="margin: 0 auto 1rem; opacity: 0.3; color: #9ca3af;">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                  </svg>
                  <p style="margin: 0; font-size: 1.1rem; color: #6b7280;">No forum discussions yet.</p>
                  <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #9ca3af;">Be the first to start a discussion!</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<!-- New Topic Modal -->
<div id="newTopicModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
    <div style="padding: 1.5rem; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
      <h2 style="margin: 0; font-size: 1.5rem; color: #111827;">Create New Discussion</h2>
      <button onclick="closeNewTopicModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #666; padding: 0; width: 32px; height: 32px; line-height: 1;">×</button>
    </div>
    
    <form id="newTopicForm" style="padding: 1.5rem;">
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Topic Title</label>
        <input 
          type="text" 
          id="topicTitle" 
          required 
          placeholder="Enter a descriptive title..." 
          style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; box-sizing: border-box;"
        />
      </div>
      
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Category</label>
        <select 
          id="topicCategory" 
          required 
          style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; box-sizing: border-box;"
        >
          <option value="">Select a category...</option>
          <option value="mental_health">Mental Health</option>
          <option value="academic">Academic Support</option>
          <option value="career">Career Guidance</option>
          <option value="personal">Personal Development</option>
          <option value="resources">Resources</option>
        </select>
      </div>
      
      <div style="margin-bottom: 1.25rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;">Description</label>
        <textarea 
          id="topicContent" 
          required 
          rows="8" 
          placeholder="Share your thoughts, ask questions, or provide guidance..." 
          style="width: 100%; padding: 0.75rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical; font-family: inherit; box-sizing: border-box;"
        ></textarea>
      </div>
      
      <div style="margin-bottom: 1.25rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" id="topicPinned" style="width: 18px; height: 18px; cursor: pointer;" />
          <span style="font-weight: 600; color: #333;">Pin this topic (keeps it at the top)</span>
        </label>
      </div>
      
      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button 
          type="button" 
          onclick="closeNewTopicModal()" 
          style="padding: 0.75rem 1.5rem; border: 1px solid #e0e0e0; background: white; border-radius: 8px; cursor: pointer; font-size: 1rem;"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          class="btnWSvg btnPrimaryText" 
          style="padding: 0.75rem 1.5rem;"
        >
          Create Topic
        </button>
      </div>
    </form>
  </div>
</div>

<script src="/js/counselor/counselorForum.js" defer></script>

<style>
.forum-item:hover {
  background: #f9fafb !important;
  border-left-color: #4f46e5 !important;
}

@media (max-width: 768px) {
  #newTopicModal > div {
    width: 95%;
    margin: 1rem;
  }
}
</style>