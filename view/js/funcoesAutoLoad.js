export function habilitarTooltip() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    
    // Se não houver tooltips, sai da função imediatamente
    if (tooltips.length === 0) return;

    // Itera diretamente sobre o NodeList (mais performático)
    tooltips.forEach(el => new bootstrap.Tooltip(el));
}

export function habilitarDropdown() {
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    
    // Se não houver elementos, encerra para poupar processamento
    if (dropdowns.length === 0) return;

    // Inicializa diretamente sem criar arrays extras na memória
    dropdowns.forEach(el => new bootstrap.Dropdown(el));
}



