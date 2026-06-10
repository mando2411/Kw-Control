<?php
$host = '127.0.0.1';
$db = 'u281896900_control';
$user = 'u281896900_control0';
$pass = '@5HeHR?EuE';
try {
    $pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8mb4', $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach ([5914, 8388, 370, 11104, 10173] as $id) {
        $stmt = $pdo->prepare('select count(*) as c from voters where alrkm_almd_yn=?');
        $stmt->execute([$id]);
        $exact = $stmt->fetchColumn();
        $stmt = $pdo->prepare('select count(*) as c from voters where alrkm_almd_yn like ?');
        $stmt->execute(['%'.$id.'%']);
        $like = $stmt->fetchColumn();
        echo "$id: exact=$exact like=$like\n";
    }
} catch (PDOException $e) {
    echo 'ERR: '.$e->getMessage();
}
