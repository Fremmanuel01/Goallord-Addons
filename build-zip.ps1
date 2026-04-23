# Builds a WordPress-compatible plugin zip with forward-slash entry names.
# Workaround for PowerShell Compress-Archive and .NET CreateFromDirectory
# both writing Windows-style backslashes on some systems.

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$ErrorActionPreference = 'Stop'

$srcRoot   = 'C:\Users\user\Documents\Tansi Seminary Elementor Plugins\goallord-addons'
$destZip   = 'C:\Users\user\Documents\Tansi Seminary Elementor Plugins\goallord-addons.zip'
$topFolder = 'goallord-addons'
$excludeDirs = @('tests')
# Bitmap images that might end up in /assets by accident (design references, screenshots, etc.)
# Our plugin assets are only css/js right now. If a real widget needs a bundled image,
# add its filename to $includeImages below.
$excludeImageExtensions = @('.jpg', '.jpeg', '.png', '.gif', '.webp', '.heic', '.bmp', '.tif', '.tiff')
$includeImages = @() # explicit whitelist of image filenames to keep

if (Test-Path $destZip) {
    Remove-Item -Force $destZip
}

# Defensive: remove any accidental nested copy of the plugin inside itself
# (which can happen if someone unpacks the zip on top of the source tree).
$nested = Join-Path $srcRoot $topFolder
if (Test-Path $nested) {
    Remove-Item -Recurse -Force $nested
    Write-Output "Cleaned nested leftover: $nested"
}
# Remove any stale zip files accidentally placed inside the plugin tree.
Get-ChildItem -Path $srcRoot -Recurse -File -Filter '*.zip' -ErrorAction SilentlyContinue | ForEach-Object {
    Remove-Item -Force $_.FullName
    Write-Output "Removed stray zip inside plugin: $($_.FullName)"
}

$fs  = [System.IO.File]::Create($destZip)
$zip = New-Object System.IO.Compression.ZipArchive($fs, [System.IO.Compression.ZipArchiveMode]::Create)

try {
    $files = Get-ChildItem -Path $srcRoot -Recurse -File
    foreach ($file in $files) {
        $relative = $file.FullName.Substring($srcRoot.Length).TrimStart('\','/')
        $excluded = $false
        foreach ($dir in $excludeDirs) {
            if ($relative.StartsWith("$dir\") -or $relative.StartsWith("$dir/")) {
                $excluded = $true
                break
            }
        }
        if ($excluded) { continue }

        # Skip image files unless whitelisted.
        $ext = [System.IO.Path]::GetExtension($file.Name).ToLower()
        if ($excludeImageExtensions -contains $ext -and -not ($includeImages -contains $file.Name)) {
            Write-Output "Skipped image (not whitelisted): $relative"
            continue
        }

        # Normalize to forward slashes — required by the zip spec and by WordPress.
        $entryName = ($topFolder + '/' + $relative).Replace('\', '/')

        $entry = $zip.CreateEntry($entryName, [System.IO.Compression.CompressionLevel]::Optimal)
        $entryStream = $entry.Open()
        try {
            $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
            $entryStream.Write($bytes, 0, $bytes.Length)
        } finally {
            $entryStream.Dispose()
        }
    }
} finally {
    $zip.Dispose()
    $fs.Dispose()
}

Write-Output "Wrote: $destZip ($([int]((Get-Item $destZip).Length / 1024)) KB)"
