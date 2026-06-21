export const SHIPMENT_STATUS_COMPLETED = 'completed';
export const SHIPMENT_STATUS_PENDING_WEIGHT = 'pending_weight';
export const SHIPMENT_STATUS_PENDING_PRICE = 'pending_price';
export const SHIPMENT_STATUS_PENDING_BOTH = 'pending_both';

export function isShipmentCompleted(status) {
    return status === SHIPMENT_STATUS_COMPLETED;
}

export function shipmentStatusBadgeClass(status) {
    switch (status) {
        case SHIPMENT_STATUS_COMPLETED:
            return 'bg-green-100 text-green-800';
        case SHIPMENT_STATUS_PENDING_WEIGHT:
        case SHIPMENT_STATUS_PENDING_PRICE:
            return 'bg-amber-100 text-amber-800';
        case SHIPMENT_STATUS_PENDING_BOTH:
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-600';
    }
}

export function formatShipmentAmount(value, formatCurrency) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    return formatCurrency(value);
}
