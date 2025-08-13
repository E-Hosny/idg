<?php
// Test PHP Upload Settings
echo "<h2>PHP Upload Settings Test</h2>";
echo "<hr>";

// Check upload settings
echo "<h3>Upload Settings:</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";
echo "<hr>";

// Check execution settings
echo "<h3>Execution Settings:</h3>";
echo "max_execution_time: " . ini_get('max_execution_time') . " seconds<br>";
echo "max_input_time: " . ini_get('max_input_time') . " seconds<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "<hr>";

// Check if settings are sufficient for 100MB uploads
$upload_limit = ini_get('upload_max_filesize');
$post_limit = ini_get('post_max_size');

$upload_bytes = return_bytes($upload_limit);
$post_bytes = return_bytes($post_limit);
$required_bytes = 100 * 1024 * 1024; // 100MB in bytes

echo "<h3>100MB Upload Test:</h3>";
echo "Required: 100MB (" . number_format($required_bytes) . " bytes)<br>";
echo "Upload limit: " . $upload_limit . " (" . number_format($upload_bytes) . " bytes)<br>";
echo "Post limit: " . $post_limit . " (" . number_format($post_bytes) . " bytes)<br>";

if ($upload_bytes >= $required_bytes && $post_bytes >= $required_bytes) {
    echo "<br><span style='color: green; font-weight: bold;'>✅ SUCCESS: System can handle 100MB uploads!</span>";
} else {
    echo "<br><span style='color: red; font-weight: bold;'>❌ FAILED: System cannot handle 100MB uploads</span>";
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
?> 