if (!(Test-Path "d:\web\CALENDAR\images")) {
    New-Item -ItemType Directory -Force -Path "d:\web\CALENDAR\images"
}

$images = @{
    "01" = "https://images.unsplash.com/photo-1483921020237-2ff51e8e4b22?w=1920&q=80"
    "02" = "https://images.unsplash.com/photo-1522383225653-ed111181a951?w=1920&q=80"
    "03" = "https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd?w=1920&q=80"
    "04" = "https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1920&q=80"
    "05" = "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1920&q=80"
    "06" = "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=80"
    "07" = "https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=1920&q=80"
    "08" = "https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=1920&q=80"
    "09" = "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1920&q=80"
    "10" = "https://images.unsplash.com/photo-1509114397022-ed747cca3f65?w=1920&q=80"
    "11" = "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=1920&q=80"
    "12" = "https://images.unsplash.com/photo-1517299321609-52687d1bc55a?w=1920&q=80"
}

foreach ($key in $images.Keys) {
    $outFile = "d:\web\CALENDAR\images\month-$key.jpg"
    Write-Host "Downloading month-$key..."
    Invoke-WebRequest -Uri $images[$key] -OutFile $outFile
}
Write-Host "All background images downloaded successfully."
