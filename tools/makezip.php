<?php
/**
 * テーマを WordPress にアップロードできる zip にまとめる。
 * PowerShell の Compress-Archive はパス区切りが \ になり、
 * WordPress 側で展開できないことがあるため PHP の ZipArchive を使う。
 */

$src = 'C:/Users/TSUCHIGA/Desktop/tatezumi/wp-theme/sobakokoro';
$out = $argv[1] ?? '';

if ($out === '') {
    exit("出力先を指定してください\n");
}

@unlink($out);

$zip = new ZipArchive();
if ($zip->open($out, ZipArchive::CREATE) !== true) {
    exit("zip を作れません\n");
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS)
);

$count = 0;
foreach ($iterator as $file) {
    $path = str_replace('\\', '/', $file->getPathname());
    $relative = ltrim(str_replace($src, '', $path), '/');
    $zip->addFile($path, 'sobakokoro/' . $relative);
    $count++;
}

$zip->close();

echo $count . " ファイルを追加しました\n";
