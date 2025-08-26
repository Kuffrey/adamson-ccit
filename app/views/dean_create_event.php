<main class="container">
  <h1>New Event</h1>
  <form method="post" action="/adamson-ccit/public/index.php?page=admin_manage_programs&section=events&action=create">
    <label>Title <input type="text" name="title" required></label><br>
    <label>Date/Time <input type="datetime-local" name="starts_at" required></label><br>
    <label>Location <input type="text" name="location"></label><br>
    <label>Description<br><textarea name="description" rows="8"></textarea></label><br>
    <button type="submit" class="btn">Publish</button>
    <a class="btn" href="?page=admin_manage_programs">Back</a>
  </form>
</main>
