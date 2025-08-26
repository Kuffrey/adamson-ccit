<main class="container">
  <h1>New Announcement</h1>
  <form method="post" action="/adamson-ccit/public/index.php?page=admin_manage_announcements&action=create">
    <label>Title <input type="text" name="title" required></label><br>
    <label>Audience
      <select name="audience">
        <option value="all">All</option>
        <option value="students">Students</option>
        <option value="faculty">Faculty</option>
        <option value="applicants">Applicants</option>
      </select>
    </label><br>
    <label>Body<br><textarea name="body" rows="8" required></textarea></label><br>
    <label><input type="checkbox" name="pinned" value="1"> Pin on top</label><br>
    <label>Expires at <input type="datetime-local" name="expires_at"></label><br>
    <button type="submit" class="btn">Publish</button>
    <a class="btn" href="?page=admin_manage_announcements">Back</a>
  </form>
</main>
