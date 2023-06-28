<?php
$mergedFilename = 'merged.pdf';
$outputDir = 'output/';

// Verifica se os arquivos foram enviados corretamente
if (!isset($_FILES['pdf-files'])) {
  echo 'Nenhum arquivo foi enviado.';
  exit;
}
$files = $_GET['pdf-files']['tmp_name'];

// $files = $_FILES['pdf-files']['tmp_name'];
$fileCount = count($files);

// Verifica se foram enviados pelo menos dois arquivos
if ($fileCount < 2) {
  echo 'Selecione pelo menos 2 arquivos PDF para mesclar.';
  exit;
}

// Carrega a biblioteca FPDI
require_once('fpdf/fpdf.php');
require_once('fpdi/fpdi.php');

// Instancia o objeto FPDI
$pdf = new FPDI();

// Itera sobre os arquivos enviados
for ($i = 0; $i < $fileCount; $i++) {
  $file = $files[$i];

  // Adiciona as páginas do arquivo PDF ao PDF mesclado
  $pageCount = $pdf->setSourceFile($file);
  for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
    $tplIdx = $pdf->importPage($pageNum);
    $pdf->AddPage();
    $pdf->useTemplate($tplIdx);
  }
}

// Salva o PDF mesclado
$mergedPath = $outputDir . $mergedFilename;
$pdf->Output($mergedPath, 'F');

// Define o cabeçalho para download do arquivo mesclado
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $mergedFilename . '"');
header('Content-Length: ' . filesize($mergedPath));
readfile($mergedPath);

// Exclui o arquivo mesclado
unlink($mergedPath);
