<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'admin/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle review submission
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $productId = $_POST['product_id']; // Retrieve the product ID from the form
    var_dump($productId);

    // Insert the review data into the database, associating it with the specific product
    $insert_query = "INSERT INTO products (id, rating, comment) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    if ($stmt) {
        $stmt->bind_param("iis", $productId, $rating, $comment);
        if ($stmt->execute()) {
            $stmt->close();
            // You can optionally send a JSON response here instead of a redirect
            echo json_encode(["success" => true]);
            exit();
        } else {
            // You can optionally send a JSON response here instead of an echo
            echo json_encode(["success" => false, "error" => "Error: " . $stmt->error]);
        }
    } else {
        // You can optionally send a JSON response here instead of an echo
        echo json_encode(["success" => false, "error" => "Error: " . $conn->error]);
    }
}
?>
