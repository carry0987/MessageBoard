<?php
class DataCreate
{
    public function __construct($connect)
    {
        $this->connectdb = $connect;
    }

    public function createUser($user)
    {
        $create_query = 'INSERT INTO user (username, password, email, is_admin, last_login, join_date) VALUES (?,?,?,?,?,?)';
        $create_stmt = $this->connectdb->stmt_init();
        try {
            $create_stmt->prepare($create_query);
            $create_stmt->bind_param('ssssss', 
                $user['username'], 
                $user['password'], 
                $user['email'], 
                $user['is_admin'], 
                $user['last_login'], 
                $user['join_date']
            );
            $create_stmt->execute();
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
    }

    public function createArticle($article)
    {
        $create_query = 'INSERT INTO article (user_id, title, content, board_id, set_sitemap, last_edit, post_date) VALUES (?,?,?,?,?,?,?)';
        $create_stmt = $this->connectdb->stmt_init();
        try {
            $create_stmt->prepare($create_query);
            $create_stmt->bind_param('ississs', 
                $article['user_id'], 
                $article['title'], 
                $article['content'], 
                $article['board_id'], 
                $article['set_sitemap'], 
                $article['last_edit'],
                $article['post_date']
            );
            $create_stmt->execute();
        } catch (Exception $e) {
            echo '<h1>Service unavailable</h1>';
            echo '<br />';
            echo '<h2>Error Info :'.$e->getMessage().'</h2>';
            exit();
        }
    }
}
