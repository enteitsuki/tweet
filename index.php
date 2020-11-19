<?php

require_once('config.php');
require_once('functions.php');

$dbh = connectDb();

$sql = 'SELECT * FROM tweets ORDER BY created_at DESC';
$stmt = $dbh->prepare($sql);
$stmt->execute();
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'];
    $errors = [];

    if ($content == '') {
        $errors['content'] = 'ツイート内容を入力してください。';
    }

    if (!$errors) {
        $dbh = connectDb();
        $sql = 'INSERT INTO tweets (content) VALUES (:content)';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: index.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tweet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>新規Tweet</h1>
    <?php if ($errors) : ?>
        <ul class="error-list">
            <?php foreach ($errors as $error) : ?>
                <li>
                    <?= h($error) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form action="" method="post">
        <div>
            <label for="content">ツイート内容</label><br>
            <textarea name="content" cols="30" rows="5" placeholder="いまどうしてる？"></textarea>
        </div>
        <div>
            <input type="submit" value="投稿する">
        </div>
    </form>
    <h1>Tweet一覧</h1>
    <?php if ($tweets): ?>
        <ul class="tweet-list">
            <?php foreach ($tweets as $tweet): ?>
                <li>
                    <a href="show.php?id=<?= h($tweet['id']) ?>"><?= h($tweet['content']) ?></a><br>
                    投稿日時: <?= h($tweet['created_at']) ?>
                    <?php if ($tweet['good'] == false): ?>
                        <a href="good.php?id=<?= h($tweet['id']) ?>" class="good-link">☆</a>
                    <?php else: ?>
                        <a href="good.php?id=<?= h($tweet['id']) ?>" class="bad-link">★</a>
                    <?php endif; ?>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div>投稿されたtweetはありません</div>
    <?php endif; ?>
</body>

</html>