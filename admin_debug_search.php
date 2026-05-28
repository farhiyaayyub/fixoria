<?php
// admin_debug_search.php — quick local debug only (remove when finished)
// Usage: /Fixoria/admin_debug_search.php?q=Fradzmier
require __DIR__ . '/auth/db.php';
$q = trim((string)($_GET['q'] ?? ''));
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Debug search</title></head><body style="font-family:system-ui,Segoe UI,Roboto,Arial;line-height:1.4;padding:20px;">
<h2>Debug: search users</h2>
<form method="get">
  <label>Query: <input name="q" value="<?php echo htmlspecialchars($q); ?>"></label>
  <button type="submit">Run</button>
</form>
<hr>
<?php
if($q === ''){
  echo '<p>Provide a query in <code>?q=</code></p>';
  echo '<p>Example: <a href="admin_debug_search.php?q=Fradzmier">admin_debug_search.php?q=Fradzmier</a></p>';
  exit;
}
try{
  $like = '%' . str_replace('%','\\%',$q) . '%';
  $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, created_at FROM users WHERE username LIKE :q OR email LIKE :q OR first_name LIKE :q OR last_name LIKE :q LIMIT 200");
  $stmt->execute(['q' => $like]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo '<h3>Found: ' . count($rows) . '</h3>';
  if(count($rows) === 0){ echo '<pre>No matching users.</pre>'; }
  else {
    echo '<table border="1" cellpadding="6" style="border-collapse:collapse">';
    echo '<tr><th>id</th><th>username</th><th>first_name</th><th>last_name</th><th>email</th><th>created_at</th></tr>';
    foreach($rows as $r){
      echo '<tr>';
      echo '<td>' . htmlspecialchars($r['id']) . '</td>';
      echo '<td>' . htmlspecialchars($r['username']) . '</td>';
      echo '<td>' . htmlspecialchars($r['first_name']) . '</td>';
      echo '<td>' . htmlspecialchars($r['last_name']) . '</td>';
      echo '<td>' . htmlspecialchars($r['email']) . '</td>';
      echo '<td>' . htmlspecialchars($r['created_at']) . '</td>';
      echo '</tr>';
    }
    echo '</table>';
  }
  echo '<h4>Raw JSON</h4><pre>' . json_encode($rows, JSON_PRETTY_PRINT) . '</pre>';
}catch(Exception $e){
  echo '<pre>error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
}
?>
</body></html>