
let data = {
    clients: [
        3.2, 15.2, 17.3, 11.4, 17.3, 9.2, 11.6
    ],
    invoices: [
        3.2, 11.9, 17.3, 9.7, 9, 12, 11.9, 10.3
    ],
    recurring: [
        3.2, 11.9, 17.3, 9.7, 9.6, 9.3, 14, 10.3
    ],
    products: [
        3.2, 17.2, 39.5, 13.4, 12
    ],
    payments: [
        3.2, 11.9, 17.3, 17.1, 7.9, 8.2, 9.5, 10.2
    ],
    expenses: [
        3.2, 11.9, 17.3, 8.5, 15.9, 9.4, 8.8, 10.3
    ],
    credits: [
        3.2, 17.4, 16.4, 16.7, 9.3, 11.2, 11.1
    ],
    quotes: [
        3.2, 18.9, 22.3, 9.7, 9, 12.1, 10.3
    ],
    vendors: [
        3.2, 12.9, 12.7, 10.3, 25.5, 9.9, 10.8
    ]
};

for (let type in data) {
    let values = data[type];
    let total = values.reduce((a, b) => a + b);

    data[type] = values.map(value => parseFloat((value / total * 100).toFixed(0)));
    total = data[type].reduce((a, b) => a + b);
    data[type].unshift(100 - total);
}

console.log(data);
