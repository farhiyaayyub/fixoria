#!/bin/bash

# Script to update git remote URLs for all repositories with the new username
# This script updates the remote URLs from the old username to farhiyaayyub

echo "Updating git remote URLs..."
echo ""

# Array of repository paths (relative to home directory or current directory)
# Update these paths if your repositories are located elsewhere
REPOS=(
  "fixoria"
  "booktrack-library-system"
  "faculty-evaluation-system"
  "mywebapp"
)

# New username
NEW_USERNAME="farhiyaayyub"

# Process each repository
for repo in "${REPOS[@]}"; do
  if [ -d "$repo" ]; then
    echo "Processing: $repo"
    cd "$repo"
    
    # Update remote URL
    git remote set-url origin "https://github.com/$NEW_USERNAME/$repo.git"
    
    # Verify the update
    echo "  New remote URL:"
    git remote -v | grep origin
    echo ""
    
    cd ..
  else
    echo "⚠️  Repository not found: $repo (skipping)"
    echo ""
  fi
done

echo "✅ All repositories have been updated!"
