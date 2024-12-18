<?php
// SQLite データベースに接続
try {
    $db = new PDO('sqlite:db.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // テーブルが存在しない場合は作成
    $db->exec("CREATE TABLE IF NOT EXISTS test (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    )");

    // サンプルデータを挿入（初回アクセス時のみ）
    if ($db->query("SELECT COUNT(*) FROM test")->fetchColumn() == 0) {
        $db->exec("INSERT INTO test (name) VALUES ('Hello Render'), ('Test User')");
    }

    // データを取得して表示
    $stmt = $db->query("SELECT * FROM test");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h1>SQLite Test Table</h1>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th></tr>";
    foreach ($results as $row) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td></tr>";
    }
    echo "</table>";

    // フォームを追加
    echo "<h2>Add a New Entry</h2>";
    echo "<form method='POST'>
        <input type='text' name='name' placeholder='Enter a name' required>
        <button type='submit'>Add</button>
    </form>";

    // フォーム送信時の処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
        $name = htmlspecialchars($_POST['name']);
        $stmt = $db->prepare("INSERT INTO test (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>
