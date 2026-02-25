<?php
/**
 * Dynamic Pricing Sync Script (BCV Rate)
 * Run this via Cron or Manual admin button
 */

require_once __DIR__ . '/../includes/db.php';

echo "Starting exchange rate sync...\n";

// Fetch BCV Rate (Simulation/Scraping)
// In a real scenario, you'd use a trusted API or stable scraping
function fetchBCVRate() {
    try {
        // Attempting to get from a public API or simulation for this demo
        // For Venezuela, BCV is the standard.
        // We'll simulate a fetch here or use a known public endpoint if available.
        $url = "https://pydolarvenezuela-api.vercel.app/api/v1/dollar?page=bcv"; 
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (isset($data['monitors']['bcv']['price'])) {
            return (float)$data['monitors']['bcv']['price'];
        }
    } catch (Exception $e) {
        error_log("Sync Error: " . $e->getMessage());
    }
    return null;
}

$newRate = fetchBCVRate();

if ($newRate) {
    try {
        $stmt = $pdo->prepare("UPDATE settings SET exchange_rate = ?, last_sync_at = NOW() WHERE id = 1 AND price_source = 'api'");
        $stmt->execute([$newRate]);
        
        if ($stmt->rowCount() > 0) {
            echo "Successfully updated rate to: $newRate Bs.\n";
        } else {
            echo "Sync skipped (Manual mode active or same rate).\n";
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
} else {
    echo "Could not fetch new rate. Check API/Internet connection.\n";
}
