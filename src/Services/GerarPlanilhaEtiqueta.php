<?php
namespace FNDE\Services;
ob_clean();
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use FNDE\Services\ConsultarDestinatarios;
use FNDE\Models\RemetenteAR;
use FNDE\Models\DestinatarioAR;
use FNDE\Utils\GetSession;

class GerarPlanilhaEtiqueta {
    //private RemetenteAR $remetente; 
    //private DestinatarioAR $listaDestinatarios;
    private array $remetente; 
    private array $listaDestinatarios;
    private array $dadosComplementares;
    private string $diretorioSaida = '../../planilhaEtiquetas/';

    //public function __construct($Remetente, $ListaDestinatarios, $DadosComplementares) {
    public function __construct($listaEtiquetas) {    
        
        $this->separarDados($listaEtiquetas);
        //$this->remetente = $Remetente;
        //$this->listaDestinatarios = $ListaDestinatarios;
        //$this->dadosComplementares = $DadosComplementares;

        if (!file_exists($this->diretorioSaida)) {
            mkdir($this->diretorioSaida, 0777, true);
        }
    }//construtor

    public function processar() {
        $this->limparArquivosAntigos();
        $remetente = $this->remetente;
        $listaDestinatarios = $this->listaDestinatarios;
        $dadosComplementares = $this->dadosComplementares;

        $caminhoArquivo = $this->gerarPlanilha($remetente, $listaDestinatarios, $dadosComplementares);
        //$caminhoArquivo = $this->gerarPlanilha($listaEtiquetas);
        if($caminhoArquivo){
            if($this->downloadArquivo($caminhoArquivo)){
                return true;
            }else{
                return false;
            }

        }else{
            return false;
        }
        
    }//processar

    private function separarDados($listaEtiquetas){

        $remetente = [];
        $destinatario = [];
        $dadosComplementares = [];

        foreach ($listaEtiquetas as $key => $value) {
            $remetente[$key] = $value['remetente'];
            $destinatario[$key] = $value['destinatario'];
            $dadosComplementares[$key] = $value['dadosComplementares'];
        }
        $this->remetente = $remetente;
        $this->listaDestinatarios = $destinatario;
        $this->dadosComplementares = $dadosComplementares;
    }


    private function gerarPlanilha($remetente, $listaDestinatarios, $dadosComplementares) {
        
        $this->limparArquivosAntigos();

        $remetente = $this->remetente;
        $listaDestinatarios = $this->listaDestinatarios;
        $dadosComplementares = $this->dadosComplementares;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        
        $cabecalho = [
            'remetente', 'cnpj_remetente', 'cep_remetente', 'logradouro_remetente', 'numero_remetente', 
            'complemento_remetente', 'bairro_remetente', 'cidade_remetente', 'uf_remetente',
            'destinatario', 'cnpj_destinatario', 'cep_destinatario', 'logradouro_destinatario', 
            'numero_destinatario', 'complemento_destinatario', 'bairro_destinatario', 
            'cidade_destinatario', 'uf_destinatario', 'codigo_servico', 'tipo_objeto', 
            'peso', 'altura', 'largura', 'comprimento', 'observacao', 
            'item_declaracao_conteudo', 'quantidade', 'valor', 'prazo_postagem', 'copias'
        ];

        // Aplica o cabeçalho na linha 1
        $col = 'A';
        foreach ($cabecalho as $titulo) {
            $sheet->setCellValue($col . '1', $titulo);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }
        
        $linha = 2;
        // Faz o loop para cada destinatário informado
        //foreach ($listaDestinatarios as $destinatario) {
        for($i = 0; $i < count($listaDestinatarios); $i++){
            //var_dump($itemEtiqueta);
            $sheet->setCellValue('A' . $linha, $remetente[$i]->unidade.' - '.$remetente[$i]->nomeGerente);
            $sheet->setCellValue('B' . $linha, $remetente[$i]->cnpj);
            $sheet->setCellValue('C' . $linha, $remetente[$i]->cep);
            $sheet->setCellValue('D' . $linha, $remetente[$i]->logradouro);
            $sheet->setCellValue('E' . $linha, $remetente[$i]->numero);
            $sheet->setCellValue('F' . $linha, $remetente[$i]->complemento);
            $sheet->setCellValue('G' . $linha, $remetente[$i]->bairro);
            $sheet->setCellValue('H' . $linha, $remetente[$i]->cidade);
            $sheet->setCellValue('I' . $linha, $remetente[$i]->uf);

            $sheet->setCellValue('J' . $linha, $listaDestinatarios[$i]->unidade.' - '.$listaDestinatarios[$i]->nomeGestorAR);
            $sheet->setCellValue('K' . $linha, $listaDestinatarios[$i]->cnpj);
            $sheet->setCellValue('L' . $linha, $listaDestinatarios[$i]->cep);
            $sheet->setCellValue('M' . $linha, $listaDestinatarios[$i]->logradouro);
            $sheet->setCellValue('N' . $linha, $listaDestinatarios[$i]->numero);
            $sheet->setCellValue('O' . $linha, $listaDestinatarios[$i]->complemento);
            $sheet->setCellValue('P' . $linha, $listaDestinatarios[$i]->bairro);
            $sheet->setCellValue('Q' . $linha, $listaDestinatarios[$i]->cidade);
            $sheet->setCellValue('R' . $linha, $listaDestinatarios[$i]->uf);

            $sheet->setCellValue('S' . $linha, $dadosComplementares[$i]['codigoServico']);
            $sheet->setCellValue('T' . $linha, $dadosComplementares[$i]['tipoObjeto']);
            $sheet->setCellValue('U' . $linha, $dadosComplementares[$i]['peso']);
            $sheet->setCellValue('V' . $linha, $dadosComplementares[$i]['altura']);
            $sheet->setCellValue('W' . $linha, $dadosComplementares[$i]['largura']);
            $sheet->setCellValue('X' . $linha, $dadosComplementares[$i]['comprimento']);
            $sheet->setCellValue('Y' . $linha, $dadosComplementares[$i]['observacao']);
            $sheet->setCellValue('Z' . $linha, $dadosComplementares[$i]['itemDeclaracao']);
            $sheet->setCellValue('AA' . $linha, $dadosComplementares[$i]['quantidade']);
            $sheet->setCellValue('AB' . $linha, $dadosComplementares[$i]['valor']);
            $sheet->setCellValue('AC' . $linha, $dadosComplementares[$i]['prazoPostagem']);
            $sheet->setCellValue('AD' . $linha, $dadosComplementares[$i]['copias']);

            $linha++;
        
        }//foreach

        $sessao = new GetSession();
        $usuario = $sessao->retornarSessao();

        $nomeArquivo = $this->diretorioSaida . 'etiquetas_'.$usuario->matricula .'_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        if($writer->save($nomeArquivo)){
            return $nomeArquivo;
        }else{
            return false;
        }

        
    }//gerarPlanilha

    private function limparArquivosAntigos() {
        $arquivos = glob($this->diretorioSaida . "*.xlsx");
        $tempoLimite = 12 * 3600; // horas em segundos

        foreach ($arquivos as $arquivo) {
            if (time() - filemtime($arquivo) > $tempoLimite) {
                unlink($arquivo);
            }
        }
    }//limparArquivosAntigos

    private function downloadArquivo($caminho) {
        if (file_exists($caminho)) {
            if (ob_get_level()) ob_end_clean();

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($caminho) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($caminho));
            readfile($caminho);
            //return true;
            exit;
        }else{
            return false;
        }
    }//downloadArquivo

}
