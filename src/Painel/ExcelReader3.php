<?php

namespace FNDE\Painel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Exception;
use DateTime;

class ExcelReader
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new Exception("Arquivo de planilha não encontrado em: {$filePath}");
        }
        $this->filePath = $filePath;
    }

    /**
     * Processa o arquivo XLSX e retorna o payload com Metadados, Colunas e Dados
     */
    public function parse(): array
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();
        
        // PARAMETROS DO toArray:
        // null = valor nulo padrão
        // true = calcula fórmulas (pega o resultado final)
        // false = NÃO aplica formatação de máscara do Excel (pega o número puro/bruto)
        // true = indexa colunas por letra ('A', 'B', 'C')
        $rows = $sheet->toArray(null, true, false, true);

        if (empty($rows)) {
            return ['colunas' => [], 'dados' => []];
        }

        // 1. Extrai cabeçalhos da primeira linha
        $headerRow = array_shift($rows);
        $colunas = [];
        $headerMap = [];

        foreach ($headerRow as $colLetter => $colName) {
            $originalLabel = trim((string)$colName);
            if ($originalLabel === '') {
                continue;
            }

            // Gera a chave da variável higienizada
            $key = $this->sanitizeKey($originalLabel);
            $headerMap[$colLetter] = $key;

            // Mantém o rótulo original intacto para ser apresentado no modal e na tabela
            $colunas[] = [
                'chave'  => $key,
                'rotulo' => $originalLabel,
                'tipo'   => $this->inferType($key)
            ];
        }

        // 2. Extrai linhas de dados usando as chaves sanitizadas
        $dados = [];
        foreach ($rows as $row) {
            if (!array_filter($row)) {
                continue;
            }

            $item = [];
            foreach ($headerMap as $colLetter => $key) {
                $val = $row[$colLetter] ?? '';
                $item[$key] = $this->formatValue($key, $val);
            }
            $dados[] = $item;
        }

        return [
            'metadados' => [
                'dataGeracao'    => date('d/m/Y H:i:s'),
                'totalRegistros' => count($dados)
            ],
            'colunas' => $colunas,
            'dados'   => $dados
        ];
    }

    /**
     * Converte o nome original da coluna do Excel em um nome de variável válido:
     * - Remove acentos e caracteres especiais
     * - Substitui espaços e símbolos por '_'
     * - Converte para letras minúsculas
     */
    private function sanitizeKey(string $string): string
    {
        $string = mb_strtolower(trim($string), 'UTF-8');

        // Remoção de acentos e caracteres especiais
        $string = preg_replace('/[áàãâä]/u', 'a', $string);
        $string = preg_replace('/[éèêë]/u', 'e', $string);
        $string = preg_replace('/[íìîï]/u', 'i', $string);
        $string = preg_replace('/[óòõôö]/u', 'o', $string);
        $string = preg_replace('/[úùûü]/u', 'u', $string);
        $string = preg_replace('/[ç]/u', 'c', $string);

        // Substitui qualquer caractere não alfanumérico por sublinhado
        $string = preg_replace('/[^a-z0-9_]/', '_', $string);

        // Remove sublinhados duplicados e nas extremidades
        return preg_replace('/_+/', '_', trim($string, '_'));
    }

    /**
     * Infere o tipo do campo com base no nome da chave (faturamento, porta palete, etc.)
     */
    private function inferType(string $key): string
    {
        if (
            str_contains($key, 'valor') ||
            str_contains($key, 'faturamento') ||
            str_contains($key, 'subtotal') ||
            str_contains($key, 'preco') ||
            str_contains($key, 'custo')
        ) {
            return 'currency';
        }

        // Atualizado para considerar posição porta palete e variações numéricas
        if (
            str_contains($key, 'tiragem') ||
            str_contains($key, 'posicao') ||
            str_contains($key, 'porta_palete') ||
            str_contains($key, 'palete') ||
            str_contains($key, 'dias') ||
            str_contains($key, 'acervo') ||
            str_contains($key, 'quantidade') ||
            str_contains($key, 'qtd')
        ) {
            return 'number';
        }

        return 'string';
    }

    /**
     * Trata os valores de acordo com a chave e formato
     */
    private function formatValue(string $key, mixed $val): mixed
    {
        if ($val === null || $val === '') {
            return '';
        }

        // 1. TRATAMENTO DE DATAS
        if (str_contains($key, 'data') || str_contains($key, 'dt_')) {
            if (is_numeric($val)) {
                $unixTimestamp = Date::excelToTimestamp((float)$val);
                return date('d/m/Y', $unixTimestamp);
            }

            if (is_string($val)) {
                $val = trim($val);
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $val)) {
                    return $val;
                }
                try {
                    $dateObj = new DateTime($val);
                    return $dateObj->format('d/m/Y');
                } catch (Exception $e) {
                    return $val;
                }
            }
        }

        // 2. TRATAMENTO DE VALORES NUMÉRICOS / MOEDA
        if (is_numeric($val)) {
            return (float)$val;
        }

        if (is_string($val)) {
            $val = trim($val);

            // Remove R$ e espaços invisíveis
            $val = str_replace(['R$', ' ', "\xc2\xa0"], '', $val);

            // Se for padrão brasileiro (ex: "10.057,20" ou "2,55")
            if (str_contains($val, ',')) {
                $val = str_replace('.', '', $val);  // Remove ponto de milhar
                $val = str_replace(',', '.', $val);  // Troca vírgula decimal por ponto
            }

            if (is_numeric($val)) {
                return (float)$val;
            }
        }

        return $val;
    }
}