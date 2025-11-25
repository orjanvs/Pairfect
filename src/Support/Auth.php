<?php
namespace App\Support;

/**
 * Checks if the user is authenticated for HTML endpoints.
 * If not authenticated, redirects to login page.
 * Also sets session if not already started.
 */
function authenticateUserHtml() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user"]["is_logged_in"]) || $_SESSION["user"]["is_logged_in"] !== true) {
        header("Location: login.php");
        exit;
    }
}

/**
 * Checks if the user is authenticated for API endpoints.
 * If not authenticated, returns a 401 Unauthorized JSON response.
 * Also sets session if not already started.
 */
function authenticateUserApi() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION["user"]["is_logged_in"]) || $_SESSION["user"]["is_logged_in"] !== true) {
        http_response_code(401); // Unauthorized
        echo json_encode(["responseMessage" => "Unauthorized. Please log in."]);
        exit;
    }
}