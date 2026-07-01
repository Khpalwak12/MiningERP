export const REPORT_FILTER_OPTIONS = {
    sales: [
        { value: 'mine_type', type: 'mine_type', labelKey: 'fields.mine_type' },
        { value: 'shipment_number', type: 'text', labelKey: 'report_filters.shipment_number' },
        { value: 'quantity', type: 'number', labelKey: 'report_filters.quantity' },
        { value: 'price_per_ton', type: 'number', labelKey: 'report_filters.price_per_ton' },
        { value: 'total_amount', type: 'number', labelKey: 'report_filters.total_amount' },
        { value: 'created_by', type: 'text', labelKey: 'report_filters.created_by' },
        { value: 'notes', type: 'text', labelKey: 'report_filters.notes' },
    ],
    payments: [
        { value: 'customer', type: 'customer', labelKey: 'fields.customer' },
        { value: 'receipt_number', type: 'text', labelKey: 'fields.receipt_number' },
        { value: 'received_by', type: 'text', labelKey: 'fields.received_by' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'notes', type: 'text', labelKey: 'fields.notes' },
    ],
    sankari: [
        { value: 'truck_count', type: 'number', labelKey: 'fields.truck_count' },
        { value: 'price_per_truck', type: 'number', labelKey: 'fields.price_per_truck' },
        { value: 'discount', type: 'number', labelKey: 'fields.discount' },
        { value: 'total_amount', type: 'number', labelKey: 'fields.total_amount' },
        { value: 'created_by', type: 'text', labelKey: 'report_filters.created_by' },
        { value: 'notes', type: 'text', labelKey: 'fields.notes' },
    ],
    expenses: [
        { value: 'category', type: 'category', labelKey: 'fields.category' },
        { value: 'sub_category', type: 'text', labelKey: 'report_filters.sub_category' },
        { value: 'bill_number', type: 'text', labelKey: 'fields.bill_number' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'description', type: 'text', labelKey: 'fields.description' },
    ],
    employees: [
        { value: 'employee_name', type: 'text', labelKey: 'report_filters.employee_name' },
        { value: 'father_name', type: 'text', labelKey: 'fields.father_name' },
        { value: 'position', type: 'text', labelKey: 'fields.position' },
        { value: 'phone', type: 'text', labelKey: 'fields.phone' },
        { value: 'status', type: 'employee_status', labelKey: 'fields.status' },
        { value: 'salary', type: 'number', labelKey: 'fields.salary' },
    ],
    payroll: [
        { value: 'employee', type: 'employee', labelKey: 'fields.employee' },
        { value: 'receipt_number', type: 'text', labelKey: 'fields.receipt_number' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'payment_type', type: 'payment_type', labelKey: 'fields.payment_type' },
        { value: 'paid_by', type: 'text', labelKey: 'report_filters.paid_by' },
        { value: 'notes', type: 'text', labelKey: 'fields.notes' },
    ],
    inventory: [
        { value: 'item_name', type: 'text', labelKey: 'report_filters.item_name' },
        { value: 'item_code', type: 'text', labelKey: 'report_filters.item_code' },
        { value: 'category', type: 'text', labelKey: 'fields.category' },
        { value: 'quantity', type: 'number', labelKey: 'fields.quantity' },
        { value: 'movement_type', type: 'movement_type', labelKey: 'fields.movement_type' },
    ],
    'daily-production': [
        { value: 'quantity', type: 'number', labelKey: 'report_filters.quantity' },
        { value: 'total_tons', type: 'number', labelKey: 'report_filters.total_tons' },
        { value: 'created_by', type: 'text', labelKey: 'report_filters.created_by' },
        { value: 'notes', type: 'text', labelKey: 'fields.notes' },
    ],
    'monthly-production': [
        { value: 'month', type: 'text', labelKey: 'report_filters.month' },
        { value: 'quantity', type: 'number', labelKey: 'report_filters.quantity' },
        { value: 'total_tons', type: 'number', labelKey: 'report_filters.total_tons' },
        { value: 'created_by', type: 'text', labelKey: 'report_filters.created_by' },
    ],
    'profit-loss': [
        { value: 'revenue', type: 'number', labelKey: 'report_filters.revenue' },
        { value: 'expense', type: 'number', labelKey: 'report_filters.expense' },
        { value: 'net_profit', type: 'number', labelKey: 'reports.net_profit' },
    ],
    'contractor-expenses': [
        { value: 'category', type: 'category', labelKey: 'fields.category' },
        { value: 'sub_category', type: 'text', labelKey: 'report_filters.sub_category' },
        { value: 'bill_number', type: 'text', labelKey: 'fields.bill_number' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'description', type: 'text', labelKey: 'fields.description' },
    ],
    'mine-assets': [
        { value: 'name', type: 'text', labelKey: 'fields.name' },
        { value: 'related_to', type: 'text', labelKey: 'fields.related_to' },
        { value: 'quantity', type: 'number', labelKey: 'fields.quantity' },
        { value: 'unit', type: 'text', labelKey: 'fields.unit' },
        { value: 'status', type: 'text', labelKey: 'fields.status' },
        { value: 'remarks', type: 'text', labelKey: 'fields.notes' },
    ],
    'personal-ledger': [
        { value: 'contact_name', type: 'text', labelKey: 'fields.name' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'description', type: 'text', labelKey: 'fields.description' },
        { value: 'transaction_type', type: 'personal_transaction_type', labelKey: 'fields.type' },
    ],
    'personal-home-expenses': [
        { value: 'item_name', type: 'text', labelKey: 'fields.name' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'description', type: 'text', labelKey: 'fields.description' },
    ],
    machinery: [
        { value: 'item_name', type: 'text', labelKey: 'fields.name' },
        { value: 'bill_number', type: 'text', labelKey: 'fields.bill_number' },
        { value: 'amount', type: 'number', labelKey: 'fields.amount' },
        { value: 'currency', type: 'currency', labelKey: 'fields.currency' },
        { value: 'description', type: 'text', labelKey: 'fields.description' },
    ],
};

export function getFilterOptions(reportType) {
    return REPORT_FILTER_OPTIONS[reportType] ?? [];
}

export function getFilterOption(reportType, filterBy) {
    return getFilterOptions(reportType).find((option) => option.value === filterBy) ?? null;
}

export const SHIPMENT_STATUS_OPTIONS = [
    'completed',
    'pending',
    'pending_weight',
    'pending_price',
    'pending_both',
];

export const PAYROLL_PAYMENT_TYPES = ['full_salary', 'advance', 'partial'];

export const MOVEMENT_TYPES = ['in', 'out'];

export const EMPLOYEE_STATUS_OPTIONS = ['active', 'inactive'];
