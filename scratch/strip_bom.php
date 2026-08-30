<?php
$files = [
    'app/Services/Seo/SeoLinkGraderService.php',
    'app/Services/Seo/SeoGraderService.php',
    'app/Console/Commands/RunSeoAuditCommand.php'
];
foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $c = preg_replace('/^\xEF\xBB\xBF/', '', $c);
        file_put_contents($f, $c);
    }
}
echo "BOM stripped successfully\n";
