<?php

namespace FNDE\Utils;


class ApagarPlanilha
{
	private string $nomeArquivo;
	private string $caminho;
	
	public function __construct(string $nomeArquivo, string $caminho)
	{	
		$this->nomeArquivo = $nomeArquivo;
		$this->caminho = $caminho;
		$this->apagar();
	}


	protected function apagar():bool
	{
		if(unlink($this->caminho."/".$this->nomeArquivo)){
			return true;

		}else{
			return false;	
		}

	}


}



?>