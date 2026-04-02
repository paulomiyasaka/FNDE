// Seletores
const tblStatus = document.getElementById("tblStatus");
const tblAnalitico = document.getElementById("tblAnalitico");
const tblSintetico = document.getElementById("tblSintetico");

// DADOS SIMULADOS
const agrupamentos = [
    {
        id: 1001,
        data: "02/04/2026",
        estado: "DF",
        centralizadora: "Central A",
        paletes: [
            { numero: "PE00001", peso: 120.5, encomendas: 4 },
            { numero: "PE00002", peso: 98.7, encomendas: 3 }
        ],
        status: "Fechado"
    },
    {
        id: 1002,
        data: "02/04/2026",
        estado: "GO",
        centralizadora: "Central B",
        paletes: [
            { numero: "PE00100", peso: 102.2, encomendas: 2 },
            { numero: "PE00101", peso: 87.9, encomendas: 1 }
        ],
        status: "Aberto"
    }
];

// 1️⃣ STATUS
agrupamentos.forEach(a => {
    const pesoTotal = a.paletes.reduce((s, p) => s + p.peso, 0);

    tblStatus.innerHTML += `
        <tr>
            <td>${a.id}</td>
            <td>${a.data}</td>
            <td>${a.estado}</td>
            <td>${a.centralizadora}</td>
            <td>${a.paletes.length}</td>
            <td>${pesoTotal.toFixed(3)}</td>
            <td>${a.status}</td>
        </tr>
    `;
});

// 2️⃣ ANALÍTICO
agrupamentos.forEach(a => {
    a.paletes.forEach(p => {

        tblAnalitico.innerHTML += `
            <tr>
                <td>${a.id}</td>
                <td>${p.numero}</td>
                <td>${a.data}</td>
                <td>${a.estado}</td>
                <td>${a.centralizadora}</td>
                <td>${p.encomendas}</td>
                <td>${p.peso.toFixed(3)}</td>
            </tr>
        `;
    });
});

// 3️⃣ SINTÉTICO
agrupamentos.forEach(a => {

    const pesoTotal = a.paletes.reduce((s, p) => s + p.peso, 0);
    const encomendasTotal = a.paletes.reduce((s, p) => s + p.encomendas, 0);

    tblSintetico.innerHTML += `
        <tr>
            <td>${a.data}</td>
            <td>${a.estado}</td>
            <td>${a.centralizadora}</td>
            <td>1</td>
            <td>${a.paletes.length}</td>
            <td>${encomendasTotal}</td>
            <td>${pesoTotal.toFixed(3)}</td>
        </tr>
    `;
});