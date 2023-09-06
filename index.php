<?php

$userId = (int) basename($_SERVER['PHP_SELF']);

if ($userId) {
  $host = 'localhost';
  $db   = 'test';
  $user = 'root';
  $pass = 'root';
  $charset = 'utf8mb4';

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  try {
    $pdo = new PDO($dsn, $user, $pass, $opt);

    $stmt = $pdo->prepare("UPDATE balances SET balance = balance - 5 WHERE user_id = ?");
    $stmt->execute([$userId]);
    $hasUserBalanceUpdated = $stmt->rowCount() === 1;
    if (!$hasUserBalanceUpdated) {
      echo "Sorry, user with id $userId can't be found";
      return;
    }

    $stmt = $pdo->prepare("SELECT balance from balances WHERE user_id = ?");
    $stmt->execute([$userId]);
    sleep(2);
    $result = $stmt->fetch();
    echo $result['balance'];
  } catch (\PDOException $e) {
    if ($e->getCode() === '22003') {  // 'Out of range' SQLState error
      echo "Sorry, the user's balance can't get less than 0";
      return;
    }

    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }
} else {
  echo "hello";
}
