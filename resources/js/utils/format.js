export function formatCurrency(amount) {
    return formatMoney(amount, 'AFN');
}

export function formatMoney(amount, currency = 'AFN') {
    const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(amount || 0);

    return currency === 'USD' ? `$${formatted}` : `${formatted} AFN`;
}

export function formatNumber(value, decimals = 2) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: decimals,
    }).format(value || 0);
}
