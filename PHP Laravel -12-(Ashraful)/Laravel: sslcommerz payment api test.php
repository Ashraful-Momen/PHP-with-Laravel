<?php
// Initialize variables
$response_output = '';
$error_message = '';
$redirect_url = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // API endpoint
    $url = 'http://127.0.0.1:8000/api/instasure-api-payment';
    
    // Your authorization token
    $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI3IiwianRpIjoiMTg5NTEyYmFlYzAyNDkwODc3ZDJlNWQzYTEzZGZjODI3MDgzOTAwMGQwYmI2N2JhNDFmNjFkODRmZGIzNmY5OTMzNGUyY2UwMWYxZDJiMWIiLCJpYXQiOjE3MzM1NDM1MjEuMzA0MTE1MDU2OTkxNTc3MTQ4NDM3NSwibmJmIjoxNzMzNTQzNTIxLjMwNDEyMDA2Mzc4MTczODI4MTI1LCJleHAiOjE3NjUwNzk1MjEuMjk5MDUyOTUzNzIwMDkyNzczNDM3NSwic3ViIjoiNjI3Iiwic2NvcGVzIjpbXX0.KfOj1ozg0-uwxJXKYD9K_umApDBt8V2tvhjSRjjsvcouMzTdO8_PIUjIPP6oSgyzQIC8HnPKtEYzalVvECpexxSTmoP1suvE7Kkagw19Fdrkd_8WW-TAeiGZ_py0oPJt_kbRYb_0vz4nDch92GW3zQ2xP4Yh_mEx3xcyZVCA4nf2lg3DChA6CZsmIpG3PI2bkV4RTGFFMT5Ap8O-7F_9y6pg4TAxyDvQXmpceT7DkAtDndHlV9QGR238Oq94nini1cEXsMzOLMconlztXzbjlRy_8Z97uyhYzcbSZDYeW2rcVNjTVsrj8cnJsAwULnjbcsvvgcvmtZWPRmmVcJmoRabZDKE3bTd7yRqedHxEiP1KOIOSHzlH56_sJID_yhc8UvmbKgoRJwQoYKmQ0etSLsDe4TtxwguoucZJbVQl6EwgjNHgkgqvLImUedrJJjF1Xqz4eZSRcbMZQCYdRTTsiwHMHEkoLTIIZ2nj-mTkKZ-mbcCPLjHbCt9kNokX6lPEQpK-m8HTzPBlS4kA3ffdAmP-wVAyctJJYEFuJ_CL3aIvwM6_CrXaf5mOeSu_S3BHTAutboCEWnOMI5GPA-mReBRX0Z6HvvrXCtwLzjS9zh8y9l3PVthkQcbQ81A6PpJxcKmJa-CIQLPgZPUEc_WDHGfa1_EwMdOAOkXz0z_Pxhk';
    
    // Prepare the data
    $data = array(
        'category_id' => isset($_POST['category_id']) ? intval($_POST['category_id']) : 0,
        'order_id' => isset($_POST['order_id']) ? intval($_POST['order_id']) : 0,
        'order_amount' => isset($_POST['order_amount']) ? intval($_POST['order_amount']) : 0,
        'pgw_name' => isset($_POST['pgw_name']) ? $_POST['pgw_name'] : ''
    );

    // Initialize cURL
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ),
    ));

    // Execute the request
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($curl)) {
        $error_message = curl_error($curl);
    } else {
        // First, try to decode the response as regular JSON
        $decoded_response = json_decode($response, true);
        
        if ($decoded_response === null) {
            // If regular JSON decode fails, try to extract the JSON content from the Laravel response
            if (preg_match('/\[content:protected\] => ({.*?})\s/s', $response, $matches)) {
                $json_content = $matches[1];
                $decoded_response = json_decode($json_content, true);
            } elseif (preg_match('/\[original\] => Array\s*\(\s*\[success\] => (\d+)\s*\[redirect_url\] => (.*?)\s*\[msg\]/s', $response, $matches)) {
                $decoded_response = [
                    'success' => (bool)$matches[1],
                    'redirect_url' => trim($matches[2])
                ];
            }
        }
        
        if ($decoded_response) {
            if (isset($decoded_response['success']) && $decoded_response['success']) {
                if (isset($decoded_response['redirect_url'])) {
                    $redirect_url = trim($decoded_response['redirect_url']);
                    // Store response for debugging but proceed with redirect
                    $response_output = "Redirecting to: " . htmlspecialchars($redirect_url);
                    // Perform immediate redirect
                    header("Location: " . $redirect_url);
                    exit;
                }
            } else {
                $error_message = isset($decoded_response['msg']) ? $decoded_response['msg'] : 'Unknown error occurred';
            }
        } else {
            $error_message = 'Could not extract redirect URL from response';
        }
        
        // Store raw response for debugging
        if (!$redirect_url) {
            $response_output = "Raw Response:\n" . print_r($response, true);
        }
    }

    // Close cURL
    curl_close($curl);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment API Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            padding: 10px;
            margin: 10px 0;
            background-color: #ffe6e6;
            border-radius: 4px;
        }
        .response {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Payment API Test</h2>
        
        <?php if ($error_message): ?>
            <div class="error">
                Error: <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($response_output): ?>
            <div class="response">
                <?php echo htmlspecialchars($response_output); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Category ID:</label>
                <input type="number" name="category_id" required value="12">
            </div>

            <div class="form-group">
                <label>Order ID:</label>
                <input type="number" name="order_id" required value="1243">
            </div>

            <div class="form-group">
                <label>Order Amount:</label>
                <input type="number" name="order_amount" required value="570">
            </div>

            <div class="form-group">
                <label>Payment Gateway:</label>
                <select name="pgw_name" required>
                    <option value="aamarPay">aamarPay</option>
                    <option value="nagad">Nagad</option>
                    <option value="sslcommerz">SSLCommerz</option>
                </select>
            </div>

            <button type="submit">Submit Payment</button>
        </form>
    </div>

    <?php if ($redirect_url): ?>
    <script>
        // Backup JavaScript redirect in case header redirect fails
        window.location.href = <?php echo json_encode($redirect_url); ?>;
    </script>
    <?php endif; ?>
</body>
</html>
