<?php
// announcement_functions.php

function addAnnouncement($conn, $title, $message, $status, $deadline) {
    $stmt = $conn->prepare("INSERT INTO announcements (title, message, status, deadline) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $message, $status, $deadline);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function deleteAnnouncement($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM announcements WHERE announcement_id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
