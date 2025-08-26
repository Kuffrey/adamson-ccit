<main class="container">
  <h1>New News Article</h1>
  <form method="post" action="/adamson-ccit/public/index.php?page=admin_manage_news&action=create">
    <label>Title <input type="text" name="title" required></label><br>
    <label>Summary <input type="text" name="summary"></label><br>
    <label>Body<br><textarea name="body" rows="8" required></textarea></label><br>
    <button type="submit" class="btn">Publish</button>
    <a class="btn" href="?page=admin_manage_news">Back</a>
  </form>
</main>
