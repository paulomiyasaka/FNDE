<?php

namespace FNDE\Painel;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelToDatasetConverter {
    private string $excelPath;

    public function __construct(string $excelPath) {
        if (!file_exists($excelPath)) {
            throw new Exception("Arquivo Excel não encontrado no caminho: {$excelPath}");
        }
        $this->excelPath = $excelPath;
    }

    public function processar(): array {
        $spreadsheet = IOFactory::load($this->excelPath);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $colunas = [];
        $mapeamentoColunas = [];

        // 1. Processa os Cabeçalhos (Linha 1) e extrai estilos visuais
        for ($col = 'A'; $col !== $this->getProximaColuna($highestColumn); $col++) {
            $celulaCabecalho = $sheet->getCell("{$col}1");
            $rotulo = trim((string)$celulaCabecalho->getValue());

            if (empty($rotulo)) {
                continue;
            }

            // Chave normalizada para o JSON
            $chave = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $rotulo)));

            // Extrai a Cor de Fundo da célula no Excel
            $styleFill = $sheet->getStyle("{$col}1")->getFill();
            $colorArgb = $styleFill->getStartColor()->getARGB();
            $bgHex = $this->convertArgbToHex($colorArgb);

            if (!$bgHex || $bgHex === '#000000' || $bgHex === '#FFFFFF') {
                $bgHex = '#212529'; // Fundo padrão dark
            }

            $textColor = $this->calcularCorTextoContraste($bgHex);

            // Detecta tipo por formato da célula da 2ª linha
            $formatCode = $sheet->getStyle("{$col}2")->getNumberFormat()->getFormatCode();
            $tipo = $this->identificarTipoColuna($formatCode, $chave);

            $align = ($tipo === 'moeda' || $tipo === 'numero' || $tipo === 'data') ? 'center' : 'start';

            $colConfig = [
                'chave'        => $chave,
                'rotulo'       => $rotulo,
                'bg_header'    => $bgHex,
                'color_header' => $textColor,
                'align'        => $align,
                'tipo'         => $tipo
            ];

            $colunas[] = $colConfig;
            $mapeamentoColunas[$col] = $colConfig;
        }

        // 2. Processa as Linhas de Dados (A partir da Linha 2)
        $dados = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $linhaDados = [];
            $possuiConteudo = false;

            foreach ($mapeamentoColunas as $colLetter => $colConfig) {
                $celula = $sheet->getCell("{$colLetter}{$row}");
                $valor = $celula->getValue();

                if (Date::isDateTime($celula)) {
                    $valor = Date::excelToDateTimeObject($valor)->format('d/m/Y');
                } else {
                    $valor = is_null($valor) ? '' : trim((string)$valor);
                }

                if ($valor !== '') {
                    $possuiConteudo = true;
                }

                $linhaDados[$colConfig['chave']] = $valor;
            }

            if ($possuiConteudo) {
                $dados[] = $linhaDados;
            }
        }

        return [
            'colunas' => $colunas,
            'dados'   => $dados
        ];
    }

    private function getProximaColuna(string $col): string {
        $col++;
        return $col;
    }

    private function convertArgbToHex(?string $argb): ?string {
        if (!$argb || strlen($argb) < 6) return null;
        if (strlen($argb) === 8) {
            return '#' . substr($argb, 2);
        }
        return '#' . $argb;
    }

    private function calcularCorTextoContraste(string $hexColor): string {
        $hex = ltrim($hexColor, '#');
        if (strlen($hex) !== 6) return '#FFFFFF';

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 128) ? '#212529' : '#FFFFFF';
    }

    private function identificarTipoColuna(string $formatCode, string $chave): string {
        if (str_contains($chave, 'pdf') || str_contains($chave, 'link')) {
            return 'pdf';
        }
        if (str_contains($formatCode, 'R$') || str_contains($formatCode, '$')) {
            return 'moeda';
        }
        if (str_contains($formatCode, 'yy') || str_contains($formatCode, 'mm') || str_contains($chave, 'data')) {
            return 'data';
        }
        if (str_contains($formatCode, '0') || str_contains($formatCode, '#')) {
            return 'numero';
        }
        return 'texto';
    }
}