<?php

namespace FNDE\Painel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

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

    public function parse(): array
    {
        $spreadsheet = IOFactory::load($this->filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return ['colunas' => [], 'dados' => []];
        }

        // 1. Extrai cabeçalhos da primeira linha
        $headerRow = array_shift($rows);
        $colunas = [];
        $headerMap = [];

        foreach ($headerRow as $colLetter => $colName) {
            if (empty(trim((string)$colName))) continue;

            $key = $this->sanitizeKey((string)$colName);
            $headerMap[$colLetter] = $key;

            $colunas[] = [
                'chave'  => $key,
                'rotulo' => trim((string)$colName),
                'tipo'   => $this->inferType($key)
            ];
        }

        // 2. Processa linhas de dados
        $dados = [];
        foreach ($rows as $row) {
            if (!array_filter($row)) continue; // Pula linhas totalmente vazias

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

    public function sanitizeKey(string $string): string
    {
        $string = mb_strtolower(trim($string), 'UTF-8');
        $string = preg_replace('/[áàãâä]/u', 'a', $string);
        $string = preg_replace('/[éèêë]/u', 'e', $string);
        $string = preg_replace('/[íìîï]/u', 'i', $string);
        $string = preg_replace('/[óòõôö]/u', 'o', $string);
        $string = preg_replace('/[úùûü]/u', 'u', $string);
        $string = preg_replace('/[ç]/u', 'c', $string);
        $string = preg_replace('/[^a-z0-9_]/', '_', $string);
        return preg_replace('/_+/', '_', trim($string, '_'));
    }

    private function inferType(string $key): string
    {
        if (str_contains($key, 'valor') || str_contains($key, 'faturamento') || str_contains($key, 'subtotal')) {
            return 'currency';
        }
        if (str_contains($key, 'tiragem') || str_contains($key, 'palete') || str_contains($key, 'dias') || str_contains($key, 'acervo')) {
            return 'number';
        }
        return 'string';
    }

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