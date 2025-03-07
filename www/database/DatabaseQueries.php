<?php
require_once '../database/DatabaseConnection.php';

class DatabaseQueries {
    private $db;

    public function __construct() {
        $config = require '../database/config.php';
        $this->db = new DatabaseConnection($config);
    }

    public function createAdminsTable() {
        $query = <<<QUERY
            CREATE TABLE IF NOT EXISTS admins(
                adminID INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(100) NOT NULL
            )
        QUERY;

        $this->db->run($query);
    }

    public function addDefaultAdmins() {
        $defaultAdmins = [
            ['username' => 'pumaman', 'password' => password_hash('', PASSWORD_BCRYPT, ['cost' => 12])],
            ['username' => 'jp', 'password' => password_hash('letmein', PASSWORD_BCRYPT, ['cost' => 12])],
        ];

        $query = <<<QUERY
            INSERT IGNORE INTO admins (username, password)
            VALUES (:username, :password)
        QUERY;

        foreach ($defaultAdmins as $admin) {
            $this->db->run($query, [
                ':username' => $admin['username'],
                ':password' => $admin['password']
            ]);
        }
    }


    public function getting_admins() {
        $query = <<<QUERY
            SELECT username, password 
            FROM admins
        QUERY;

        return $this->db->run($query)->fetchAll();
    }

    public function storeUserToken($userID, $token, $expiry) {
        $query = <<<QUERY
            INSERT INTO admin_tokens (UserID, Token, Expiry)
            VALUES (:userID, :token, :expiry)
            ON DUPLICATE KEY UPDATE
            Token = :token, Expiry = :expiry
        QUERY;

        return $this->db->run($query, [
            ':userID' => $userID,
            ':token' => $token,
            ':expiry' => $expiry
        ]);
    }

    public function fetchUserByToken($token) {
        $query = <<<QUERY
            SELECT u.UserID, u.FirstName, u.LastName, t.Expiry
            FROM admin_tokens t
            JOIN users u ON t.UserID = u.UserID
            WHERE t.Token = :token
        QUERY;

        return $this->db->run($query, [':token' => $token])->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTokenExpiry($userID, $expiry) {
        $query = <<<QUERY
            UPDATE admin_tokens
            SET Expiry = :expiry
            WHERE UserID = :userID
        QUERY;

        return $this->db->run($query, [
            ':expiry' => $expiry,
            ':userID' => $userID
        ]);
    }

    public function deleteUserToken($token) {
        $query = <<<QUERY
            DELETE FROM admin_tokens
            WHERE Token = :token
        QUERY;

        $this->db->run($query, [':token' => $token]);
    }

    public function validateUserToken($token) {
        $query = <<<QUERY
            SELECT UserID, Expiry
            FROM admin_tokens
            WHERE Token = :token
        QUERY;

        return $this->db->run($query, [':token' => $token])->fetch(PDO::FETCH_ASSOC);
    }

    public function get_photos_with_users() {
        $sortColumn = $_GET['sort'] ?? 'ImageID';
        $allowedSortColumns = ['ImageID', 'Title', 'UserID'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'ImageID';
        }

        $query = "
            SELECT i.ImageID, i.Title, i.Path, i.ContinentCode, u.UserID, u.FirstName, u.LastName,
                   CASE WHEN f.ImageID IS NOT NULL THEN 1 ELSE 0 END AS IsFlagged
            FROM imagedetails i
            JOIN users u ON i.UserID = u.UserID
            LEFT JOIN photo_flags f ON i.ImageID = f.ImageID
            WHERE i.ImageID NOT IN (SELECT ImageID FROM photo_deletions)
            ORDER BY $sortColumn";

        return $this->db->run($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function flag_photo($imageID) {
        $query = "INSERT INTO photo_flags (ImageID) VALUES (:imageID)";
        $this->db->run($query, [':imageID' => $imageID]);
    }

    public function delete_photo($imageID) {
        $query = "INSERT INTO photo_deletions (ImageID) VALUES (:imageID)";
        $this->db->run($query, [':imageID' => $imageID]);
    }

    public function unflag_photo($imageID) {
        $query = "DELETE FROM photo_flags WHERE ImageID = :imageID";
        $this->db->run($query, [':imageID' => $imageID]);
    }

    public function all_flagged_images() {
        $query = "SELECT ImageID FROM photo_flags";
        return $this->db->run($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function present_in_table($a, $b) {
        $query = "
            SELECT i.ImageID, i.Title, i.Path,
                   CASE WHEN f.ImageID IS NOT NULL THEN 1 ELSE 0 END AS IsFlagged
            FROM imagedetails i
            LEFT JOIN photo_deletions d ON i.ImageID = d.ImageID
            LEFT JOIN photo_flags f ON i.ImageID = f.ImageID
            WHERE d.ImageID IS NULL
        ";
        return $this->db->run($query)->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_total_photos() {
        $query = "
            SELECT COUNT(*) as total 
            FROM imagedetails i
            LEFT JOIN photo_deletions d ON i.ImageID = d.ImageID
            WHERE d.ImageID IS NULL";
        return $this->db->run($query)->fetchColumn();
    }

    public function get_most_popular_city() {
        $query = "
            SELECT c.AsciiName as CityName, COUNT(i.ImageID) as photo_count
            FROM imagedetails i
            LEFT JOIN photo_deletions d ON i.ImageID = d.ImageID
            JOIN cities c ON i.CityCode = c.CityCode
            WHERE d.ImageID IS NULL
            GROUP BY c.AsciiName
            ORDER BY photo_count DESC
            LIMIT 1";
        return $this->db->run($query)->fetch(PDO::FETCH_ASSOC);
    }

    public function get_flagged_users() {
        $query = "
            SELECT DISTINCT u.FirstName, u.LastName 
            FROM users u
            JOIN imagedetails i ON u.UserID = i.UserID
            JOIN photo_flags f ON i.ImageID = f.ImageID";
        return $this->db->run($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$ JS Views: $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

    function search_country($data) {
        $search = "$data%";

        $query = <<<QUERY
            SELECT countries.CountryName AS name, COUNT(photos.ImageID) AS photoCount, GROUP_CONCAT(CONCAT(users.FirstName, ' ', users.LastName) SEPARATOR ', ') AS UserNames
            FROM countries
            INNER JOIN imagedetails AS photos ON photos.CountryCodeISO = countries.ISO
            LEFT JOIN photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            LEFT JOIN users ON photos.UserID = users.UserID
            WHERE LOWER(countries.CountryName) LIKE LOWER(:search)
            GROUP BY countries.CountryName
            HAVING COUNT(photos.ImageID) > 0
        QUERY;

        return $this->db->run($query, [':search' => $search])->fetchAll(PDO::FETCH_ASSOC);
    }

    function search_city($data) {
        $search = "$data%";

        $query = <<<QUERY
            SELECT cities.AsciiName AS name, 
            COUNT(photos.ImageID) AS photoCount,
            GROUP_CONCAT(CONCAT(users.FirstName, ' ', users.LastName) SEPARATOR ', ') AS UserNames
            FROM cities
            INNER JOIN imagedetails AS photos ON photos.CityCode = cities.CityCode
            LEFT JOIN photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            LEFT JOIN users ON photos.UserID = users.UserID
            WHERE LOWER(cities.AsciiName) LIKE LOWER(:search)
            GROUP BY cities.AsciiName
            HAVING COUNT(photos.ImageID) > 0
        QUERY;

        return $this->db->run($query, [':search' => $search])->fetchAll(PDO::FETCH_ASSOC);
    }

    function search_continent($data) {
        $search = "$data%";

        $query = <<<QUERY
            SELECT continents.ContinentName AS name, 
            COUNT(photos.ImageID) AS photoCount,
            GROUP_CONCAT(CONCAT(users.FirstName, ' ', users.LastName) SEPARATOR ', ') AS UserNames
            FROM continents
            INNER JOIN imagedetails AS photos ON photos.ContinentCode = continents.ContinentCode
            LEFT JOIN photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            LEFT JOIN users ON photos.UserID = users.UserID
            WHERE LOWER(continents.ContinentName) LIKE LOWER(:search)
            GROUP BY continents.ContinentName
            HAVING COUNT(photos.ImageID) > 0
    QUERY;

        return $this->db->run($query, [':search' => $search])->fetchAll(PDO::FETCH_ASSOC);
    }

    // $$$$$$$$$$$$$$$$$$$$$$$$$$$$$ JS Matching Views: $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

    function matching_city($name) {
        $query = <<<QUERY
            SELECT photos.ImageID, 
            photos.Path, 
            country.CountryName, 
            city.AsciiName AS City, 
            user.FirstName, 
            user.LastName
            FROM 
            imagedetails AS photos
            INNER JOIN 
            cities AS city ON city.CityCode = photos.CityCode
            INNER JOIN 
            countries AS country ON country.ISO = photos.CountryCodeISO
            INNER JOIN 
            users AS user ON user.UserID = photos.UserID
            LEFT JOIN 
            photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            WHERE 
            deleted.ImageID IS NULL
            AND city.AsciiName = :name;
            QUERY;

        $params = ['name' => $name];
        return $this->db->run($query, $params)->fetchAll();
    }

    function matching_country($name) {
        $query = <<<QUERY
            SELECT 
                photos.ImageID, 
                photos.Path, 
                country.CountryName, 
                city.AsciiName AS City, 
                user.FirstName, 
                user.LastName
            FROM 
                imagedetails AS photos
            INNER JOIN 
                cities AS city ON city.CityCode = photos.CityCode
            INNER JOIN 
                countries AS country ON country.ISO = photos.CountryCodeISO
            INNER JOIN 
                users AS user ON user.UserID = photos.UserID
            LEFT JOIN 
                photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            WHERE 
                deleted.ImageID IS NULL
                AND country.CountryName = :name;
        QUERY;

        $params = ['name' => $name];
        return $this->db->run($query, $params)->fetchAll();
    }


    function matching_continent($name) {
        $query = <<<QUERY
            SELECT 
                photos.ImageID, 
                photos.Path, 
                country.CountryName, 
                city.AsciiName AS City, 
                user.FirstName, 
                user.LastName
            FROM 
                imagedetails AS photos
            INNER JOIN 
                cities AS city ON city.CityCode = photos.CityCode
            INNER JOIN 
                countries AS country ON country.ISO = photos.CountryCodeISO
            INNER JOIN 
                users AS user ON user.UserID = photos.UserID
            LEFT JOIN 
                photo_deletions AS deleted ON deleted.ImageID = photos.ImageID
            INNER JOIN
                continents AS continent ON continent.ContinentCode = photos.ContinentCode
            WHERE 
                deleted.ImageID IS NULL
                AND continent.ContinentName = :name;
        QUERY;

        $params = ['name' => $name];
        return $this->db->run($query, $params)->fetchAll();
    }

    public function disconnect() {
        $this->db->close_connection();
    }
}
