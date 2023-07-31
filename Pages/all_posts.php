<?php
require_once('../classes/Post.php');
require_once('../config/database.php');

    $post = new Post($pdo);

    $allPosts = $post->readAllPosts();

    if($allPosts)
    print_r($allPosts);



    ?>