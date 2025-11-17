# AutoGitPush.ps1
# Automatically commits and pushes changes to GitHub (main branch)

$RepoPath = "C:\Users\User\Heritage"
$Branch = "main"

# Go to the repo folder
Set-Location $RepoPath

Write-Host "Auto Git Push Script Started..."
Write-Host "Monitoring repo: $RepoPath on branch $Branch"

while ($true) {
    try {
        # Remove git lock file if it exists
        if (Test-Path ".git\index.lock") {
            Remove-Item ".git\index.lock" -Force
        }

        # Check if there are changes
        $status = git status --porcelain
        if ($status) {
            Write-Host "Changes detected, committing..."

            git add .
            git commit -m "Project updated"

            git pull origin $Branch --rebase
            git push origin $Branch

            Write-Host "Changes committed & pushed at $(Get-Date -Format 'HH:mm:ss')"
        } else {
            Write-Host "No changes to commit at $(Get-Date -Format 'HH:mm:ss')"
        }
    }
    catch {
        Write-Host "Error: $($_.Exception.Message)"
    }

    # Wait 2 seconds before next check
    Start-Sleep -Seconds 2
}
