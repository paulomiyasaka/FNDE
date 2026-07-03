import pandas as pd

print("⏳ Baixando e processando a base geográfica completa com ambos os formatos de fuso...")

# 1. Links dos arquivos brutos
url_municipios = "https://raw.githubusercontent.com/kelvins/municipios-brasileiros/main/csv/municipios.csv"
url_estados = "https://raw.githubusercontent.com/kelvins/municipios-brasileiros/main/csv/estados.csv"

df_mun = pd.read_csv(url_municipios)
df_est = pd.read_csv(url_estados)

# 2. Cruzamento das tabelas
df_completo = pd.merge(df_mun, df_est, on="codigo_uf", suffixes=('_mun', '_est'))

# 3. Dicionário de tradução para converter o texto em número de horas
mapeamento_fuso_horas = {
    'America/Sao_Paulo': -3,
    'America/Bahia': -3,
    'America/Belem': -3,
    'America/Fortaleza': -3,
    'America/Recife': -3,
    'America/Maceio': -3,
    'America/Araguaina': -3,
    'America/Manaus': -4,
    'America/Cuiaba': -4,
    'America/Campo_Grande': -4,
    'America/Porto_Velho': -4,
    'America/Boa_Vista': -4,
    'America/Rio_Branco': -5,
    'America/Noronha': -2
}

# 4. Cria a nova coluna numérica mapeando a coluna de texto existente
df_completo['fuso_horas'] = df_completo['fuso_horario'].map(mapeamento_fuso_horas).fillna(-3)

# 5. Organização final selecionando TODAS as colunas desejadas
df_final = df_completo[[
    'codigo_ibge', 
    'nome_mun', 
    'uf', 
    'nome_est',
    'regiao',
    'ddd',
    'fuso_horario',  # Mantém o formato texto (ex: America/Rio_Branco)
    'fuso_horas',    # Adiciona o formato numérico (ex: -5)
    'latitude_mun', 
    'longitude_mun'
]].copy()

df_final.columns = [
    'codigo_municipio_ibge', 
    'nome_municipio', 
    'uf', 
    'nome_estado',
    'regiao',
    'ddd',
    'fuso_horario',
    'fuso_horas',
    'latitude', 
    'longitude'
]

# 6. Ordenação por Estado e Nome do Município
df_final = df_final.sort_values(by=['uf', 'nome_municipio']).reset_index(drop=True)

# 7. Exportação final para o CSV
nome_arquivo = "municipios_base_completa.csv"
df_final.to_csv(nome_arquivo, index=False, encoding='utf-8-sig')

print("\n✅ Base atualizada com sucesso!")
print(f"📁 Arquivo gerado: '{nome_arquivo}'")
print("\n👀 Amostra de como os campos ficaram estruturados:")
print(df_final[['uf', 'nome_municipio', 'fuso_horario', 'fuso_horas']].iloc[[10, 2500, 5000]])