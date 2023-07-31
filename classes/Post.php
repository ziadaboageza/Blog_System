<?php

Class Post {

    private $pdoConnection;
    private $table = "posts";
    public $id;


    public function __construct($pdo) {
        $this->pdoConnection = $pdo; 
    }

    public function createPost($title, $content , $user_id) {

        $SqlQuery = "INSERT INTO ".$this->table ."(title, content,user_id) VALUES (:title, :content, :user_id)";
       
        try {
            $stmt = $this->pdoConnection->prepare($SqlQuery);

            // Bind the values to the placeholders in the prepared statement
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':user_id', $user_id);
             
            $stmt->execute();

            // Return the ID of the newly created post
            return true;
        } catch(PDOException $e) {

            echo $e->getMessage();
            return false;
            
        }
    }

    public function readAllPosts() {

    
        $SqlQuery = 'SELECT ' . $this->table . '.* , users.username FROM ' . $this->table . ' LEFT JOIN users
            ON(' . $this->table . '.user_id = users.id
            )ORDER BY ' . $this->table . '.created_at DESC' ;
            
            try {
                
                $stmt = $this->pdoConnection->prepare($SqlQuery);
                $stmt->execute();

                // Fetch the results as an associative array
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $results;

            } catch(PDOException $e) {

                echo $e->getMessage();
                return false;
                
            }

    }


    function readOne($id)
    {

        $SqlQuery = "SELECT p.title, p.content , p.user_id , p.created_at, u.username as author_name 
        FROM " . $this->table . " p INNER JOIN users u
         ON p.user_id = u.id WHERE p.id = ? LIMIT 0,1";         
        
        try {
           
            $stmt = $this->pdoConnection->prepare($SqlQuery);

            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            
            return $post;
            
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }


    function delete()
    {
        $SqlQuery = "DELETE FROM " . $this->table . " WHERE id=:id";

        try {
            $stmt = $this->pdoConnection->prepare($SqlQuery);

            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));

            $stmt->bindParam(":id", $this->id);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function update($title, $content, $id)
    {
        // SQL query to update an existing post in the database
        $SqlQuery = "UPDATE " . $this->table . " SET title=:title, content=:content WHERE id=:id";

        $stmt = $this->pdoConnection->prepare($SqlQuery);

        // Clean data
        $title = htmlspecialchars(strip_tags($title));
        $content = htmlspecialchars(strip_tags($content));
        $id = htmlspecialchars(strip_tags($id));

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }



}
?>