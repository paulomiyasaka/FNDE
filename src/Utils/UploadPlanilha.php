<?php

namespace FNDE\Utils;


use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;


/**
 * 
 */
class UploadPlanilha{

	private string $nomeArquivo;
	private string $nomeArquivoTemporario;
	private string $diretorio;

	public function __construct(string $nomeArquivo, string $nomeArquivoTemporario, string $diretorio)
	{

		$this->nomeArquivo = $nomeArquivo;
		$this->nomeArquivoTemporario = $nomeArquivoTemporario;
		$this->diretorio = $diretorio;
		$this->upload();

	}

	
	public function upload():bool
	{

		$nomeArquivo = $this->nomeArquivo;
		$nomeArquivoTemporario = $this->nomeArquivoTemporario;
		$uploadDir = $this->diretorio;

		// Validações básicas
		$allowed = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $nomeArquivoTemporario);
		finfo_close($finfo);

		if (!in_array($mime, $allowed, true)) {
		    //echo 'Tipo de arquivo não permitido';
		    //return 'Tipo de arquivo não permitido';
		    return false;
		    //exit;
		}

		// Move para local temporário seguro (opcional)
		//$uploadDir = __DIR__ . $this->localUpload;
		

		if (!is_dir($uploadDir)) {
		    mkdir($uploadDir, 0755, true);
		}
		$target = $uploadDir . '/' . basename($nomeArquivo);
		if (!move_uploaded_file($nomeArquivoTemporario, $target)) {
		    //echo 'Falha ao salvar o arquivo';
		    //return 'Falha ao salvar o arquivo';
		    return false;
		    //exit;
		}else{
			return true;
		}


	}//upload


	




}


?>