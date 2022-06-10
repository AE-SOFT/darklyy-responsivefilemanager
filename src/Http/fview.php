<?php


use Darkeum\ResponsiveFileManager\RFM;

$local_file_path_to_download = RFM::getLocalFileFromEncrypted(request()->get('ox'));

header('Content-Description: File Display');
header('Content-Type: ' . mime_content_type($local_file_path_to_download));
header("Content-Transfer-Encoding: Binary");
header('Content-Disposition: inline; filename="' . basename($local_file_path_to_download) . '"');
header('Expires: 0');
header("Cache-Control: post-check=0, pre-check=0");
header('Pragma: public');
header('Content-Length: ' . filesize($local_file_path_to_download));
readfile($local_file_path_to_download);
exit;
