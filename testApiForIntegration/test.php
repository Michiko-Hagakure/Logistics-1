// if magrequest na ng budget papunta sa ibang dept
if (isset($_POST['request_budget'])) {
    $projectID = $_POST['projectID'];


    // Step 2: Get budget from local project
    $stmt = $conn->prepare("SELECT budget FROM projects WHERE projectID = ?");
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $stmt->bind_result($budgetAmount);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    if ($budgetAmount === null) $budgetAmount = 0;

    // Step 3: Prepare API request
    // copy the url instead
    $apiUrl = "http://127.0.0.1/logistics%201/budget_requests.php";
    $apiKey = "SECRET123";

    $postData = json_encode([
        "projectID" => $projectID,
        "budgetAmount" => $budgetAmount
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-API-KEY: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

    // Debugging options
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_HEADER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Separate headers and body
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $responseBody = substr($response, $header_size);

    // Decode JSON body safely
    $result = json_decode($responseBody, true);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // Debug print for testing
    echo "<pre>";
    echo "HTTP Code: $httpCode\n";
    echo "cURL Error: $curlError\n";
    echo "Response Body: $responseBody\n";
    echo "</pre>";

    if ($curlError) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Unable to reach API: " . addslashes($curlError) . "',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } elseif ($httpCode !== 200 || empty($result['success']) || !$result['success']) {
        $errorMsg = $result['message'] ?? 'Unknown API error.';
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'API Error',
                    text: '" . addslashes($errorMsg) . "',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Budget Request Sent',
                    text: '" . addslashes($result['message']) . "',
                    confirmButtonText: 'OK'
                });
            };
        </script>";
    }

    exit;
}
