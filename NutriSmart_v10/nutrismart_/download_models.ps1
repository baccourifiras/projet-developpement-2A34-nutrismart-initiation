# Script PowerShell — Téléchargement des modèles face-api.js en local
# Exécuter depuis le dossier nutrismart_ :
#   powershell -ExecutionPolicy Bypass -File download_models.ps1

$baseUrl  = "https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights"
$dest     = "$PSScriptRoot\public\models"

# Créer le dossier si absent
New-Item -ItemType Directory -Force -Path $dest | Out-Null

$files = @(
    # TinyFaceDetector
    "tiny_face_detector_model-weights_manifest.json",
    "tiny_face_detector_model-shard1",

    # FaceLandmark68TinyNet
    "face_landmark_68_tiny_model-weights_manifest.json",
    "face_landmark_68_tiny_model-shard1",

    # FaceRecognitionNet
    "face_recognition_model-weights_manifest.json",
    "face_recognition_model-shard1",
    "face_recognition_model-shard2"
)

foreach ($file in $files) {
    $url     = "$baseUrl/$file"
    $outPath = "$dest\$file"
    Write-Host "Téléchargement : $file ..." -NoNewline
    try {
        Invoke-WebRequest -Uri $url -OutFile $outPath -UseBasicParsing
        Write-Host " OK" -ForegroundColor Green
    } catch {
        Write-Host " ERREUR : $_" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Terminé ! Modèles dans : $dest" -ForegroundColor Cyan
