# AutoGitPush_Heritage.ps1
# Automatically commits & pushes changes to your Heritage repo

$RepoPath = "C:\Users\User\Heritage"
$Branch = "main"

Write-Host "🔄 Auto Git Push Script Started..."
Write-Host "Monitoring repo: $RepoPath on branch $Branch"

while ($true) {
    try {
        # Step 1: Go to repo
        Set-Location $RepoPath

        # Step 2: Ensure .gitignore is not blocking project
        $gitignorePath = Join-Path $RepoPath ".gitignore"
        if (Test-Path $gitignorePath) {
            $content = Get-Content $gitignorePath
            if ($content -match "Bank-Management-System/") {
                Write-Host "⚠️ Found wrong ignore rule in .gitignore → Fixing..."
                $fixed = $content | Where-Object { $_ -notmatch "Bank-Management-System/" }
                Set-Content $gitignorePath $fixed
                git rm -r --cached .
                git add .
                git commit -m "Fix: removed Bank-Management-System ignore rule"
                git push origin $Branch
            }
        }

        # Step 3: Stage, commit, and push changes
        git add .
        git commit -m "Project Updated" 2>$null

        git pull origin $Branch --rebase
        git push origin $Branch

        Write-Host "✅ Changes pushed at $(Get-Date -Format 'HH:mm:ss')"
    }
    catch {
        Write-Host "❌ Error: $($_.Exception.Message)"
    }

    # Step 4: Wait 2 seconds before checking again
    Start-Sleep -Seconds 2
}
