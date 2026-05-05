<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$url = $_ENV['SUPABASE_URL'];
$reference_id = preg_replace('|https?://(.+?)\.supabase\.co|', '$1', $url);

$supabase = new Supabase\CreateClient(
    $_ENV['SUPABASE_KEY'],    
    $reference_id
);   

$message = null;
$data = null;
$error = null;

if (array_key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] === 'POST' && array_key_exists('post_text', $_POST)) {
    try {
        $post_text = trim($_POST['post_text']);
        
        if (empty($post_text)) {
            $error = 'Post text cannot be empty';
        } else {
            // Insert the new post
            $supabase->query->from('Posts')->insert([
                'post_text' => $post_text
            ])->execute();
            
            $message = 'Post added successfully!';
        }
    } catch (Exception $e) {
        $error = 'Error adding post: ' . $e->getMessage();
    }
}

//actual query to get posts from database
try {
    $response = $supabase->query->from('Posts')->select('*')->execute();
    $data = $response->data;
} catch (Exception $e) {
    $error = $e->getMessage();
}

//debug: see what we got from database
try {
    $response = $supabase->query->from('Posts')->select('*')->execute();
    $data = $response->data;
    
    // Debug: see what we got
    ?>
    <p>
        Debug: <?= print_r($data) ?>
    </p>
    <?php

} catch (Exception $e) {
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supabase Test</title>
</head>
<body>
    <h1>Supabase Test</h1>
    
    <?php if ($message): ?>
        <p> <?=htmlspecialchars($message) ?> </p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p> <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <textarea name="post_text" placeholder="Enter your post text" required="required" rows="4"></textarea>
        <button type="submit">Add Post</button>
    </form>
    
    <h2>Posts</h2>
    <?php if (!empty($data)): ?>
        <ul>
            <?php foreach ($data as $post): ?>
                <li>
                <?= htmlspecialchars($post['post_text'] ?? 'Empty post') ?> 
                <small>(<?= htmlspecialchars($post['created_at']) ?>)</small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No posts yet.</p>
    <?php endif; ?>
</body>
</html>