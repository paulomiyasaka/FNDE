<?php
namespace FNDE\Utils;

require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;	
/**
 * 
 */
class LerPlanilha
{

	private string $nomeArquivo;
	private string $caminho;
	public array $dados = [];

	public function __construct(string $nomeArquivo, string $caminho)
	{
		$this->nomeArquivo = $nomeArquivo;
		$this->caminho = $caminho;
		$this->dados = $this->ler();
	}

	public function ler(): array
	{

		try {
			//$planilha = $this->planilha;
			$planilha = $this->nomeArquivo;
			$diretorio = $this->caminho;
			$localPlanilha = $diretorio."/".$planilha;
		    //$reader = new Xlsx();

		    // Identifica automaticamente o tipo de arquivo (Xlsx, Xls, Csv, etc)
            $tipoArquivo = IOFactory::identify($localPlanilha);
            $reader = IOFactory::createReader($tipoArquivo);
		    $reader->setReadDataOnly(true);
		    $spreadsheet = $reader->load($localPlanilha);
		    
		    $sheet = $spreadsheet->getActiveSheet();

		    // Método simples: toArray
		    $rows = $sheet->toArray(null, true, true, true);

		    // Get the highest row and column that actually contain data
		    $highestRow = $sheet->getHighestDataRow(); // e.g., 10
		    $highestColumn = $sheet->getHighestDataColumn(); // e.g., 'F'

		    // Se a planilha estiver vazia, retorna array vazio
            if ($highestRow === 0) {
                return [];
            }

		    // Create the specific cell range using the determined boundaries
		    // The range starts from 'A1' and goes up to the highest data column and row found
		    $range = 'A1:' . $highestColumn . $highestRow;

		    // Pass the precise range to rangeToArray() to get only the relevant data
		    $data = $sheet->rangeToArray(
		        $range,
		        NULL,      // Value that should be returned for empty cells
		        TRUE,      // Should formulas be calculated?
		        FALSE,     // Should the array be indexed by cell row and column?
		        FALSE      // Should the array ignore the first column?
		    );
		    
		    return $data;


		} catch (\Throwable $e) {
		    //echo 'Erro ao ler a planilha: ' . $e->getMessage();
		    //return 'Erro ao ler a planilha: ' . $e->getMessage();
		    //return false;
		    return [];
		    //exit;
		}

		


	}//ler


}



?>