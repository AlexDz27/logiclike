$userId = $router->getLastPartOfUri();

if (!isUserIdFormat($userId)) {  // e.g., it should a number, or a hash like md5
	echo "Sorry, the format of the user ID is not correct.";
	return;
}

try {
	$pdo = new PDO(...);

	$stmt = $pdo->prepare("UPDATE balances SET balance = balance - 5 WHERE user_id = ?");
  $stmt->execute([$userId]);
  $hasUserBalanceUpdated = $stmt->rowCount() === 1;  // check how many rows were affected. If only 1 row was affected, then the query was successful
  if (!$hasUserBalanceUpdated) {
    echo "Sorry, user with id $userId can't be found";
    return;
  }

  $stmt = $pdo->prepare("SELECT balance from balances WHERE user_id = ?");
  $stmt->execute([$userId]);
  $result = $stmt->fetch();
  echo $result['balance']; // print out the balance after the operation
} catch (\PDOException $e) {
	if ($e->getCode() === '22003') {  // 'Out of range' SQLState error. Could use constant here, of course
    echo "Sorry, the user's balance can't get less than 0";
    return;
  }

  throw new \PDOException($e->getMessage(), (int)$e->getCode());
}