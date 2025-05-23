<?php
header("Content-Type: application/json");

$validApiKey = "SECRET123";
$headers = getallheaders();

if (!isset($headers['X-API-KEY']) || $headers['X-API-KEY'] !== $validApiKey) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Invalid API Key']);
    exit;
}

$conn = new mysqli("localhost", "root", "Michiko", "test_avalon");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$projectID = $input['projectID'] ?? null;
$budgetAmount = $input['budgetAmount'] ?? 0;

if ($projectID === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing projectID']);
    exit;
}

$sql = "INSERT INTO budget_requests (projectID, requestDate, status, budgetAmount) VALUES (?, NOW(), 'Pending', ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("id", $projectID, $budgetAmount);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Budget request inserted']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
