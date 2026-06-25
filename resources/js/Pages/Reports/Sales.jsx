import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { formatShipmentAmount, isShipmentCompleted, shipmentStatusBadgeClass } from '@/utils/shipmentStatus';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';
import usePermission from '@/hooks/usePermission';

function ReportPage({ title, rows, filters, columns, exportType, statusFilter = true }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const [status, setStatus] = useState(filters?.status || '');
    const list = Array.isArray(rows) ? rows : (rows?.data ?? []);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(window.location.pathname, {
            date_from: dateFrom,
            date_to: dateTo,
            status,
        }, { preserveState: true });
    };

    const exportQuery = `date_from=${dateFrom}&date_to=${dateTo}&status=${status}`;

    return (
        <ErpLayout>
            <Head title={title} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{title}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <form onSubmit={handleFilter} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                {statusFilter && (
                    <div>
                        <label className="block text-sm font-medium text-gray-700">{t('fields.status')}</label>
                        <select className="mt-1 rounded-md border-gray-300 text-sm" value={status} onChange={(e) => setStatus(e.target.value)}>
                            <option value="">{t('actions.filter')} — {t('fields.status')}</option>
                            <option value="completed">{t('shipments.statuses.completed')}</option>
                            <option value="pending">{t('shipments.statuses.pending')}</option>
                            <option value="pending_weight">{t('shipments.statuses.pending_weight')}</option>
                            <option value="pending_price">{t('shipments.statuses.pending_price')}</option>
                            <option value="pending_both">{t('shipments.statuses.pending_both')}</option>
                        </select>
                    </div>
                )}
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
                {can('reports.export') && exportType && (
                    <ActionButtons
                        downloadHref={route('reports.export.excel', exportType) + `?${exportQuery}`}
                        downloadLabel={t('actions.export_excel')}
                        printHref={route('reports.export.pdf', exportType) + `?${exportQuery}`}
                        printLabel={t('actions.export_pdf')}
                    />
                )}
            </form>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>{columns.map((col) => <th key={col.key} className="px-4 py-3 text-left">{col.label}</th>)}</tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={columns.length} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row, i) => (
                            <tr key={row.id ?? i} className={row.status && !isShipmentCompleted(row.status) ? 'bg-amber-50/40' : ''}>
                                {columns.map((col) => <td key={col.key} className="px-4 py-3">{col.render(row)}</td>)}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}

export default function Sales({ rows, filters }) {
    const { t } = useTranslation();
    return (
        <ReportPage
            title={t('reports.sales')}
            rows={rows}
            filters={filters}
            exportType="sales"
            columns={[
                { key: 'date', label: t('fields.date'), render: (r) => r.shipment_date_shamsi || r.shipment_date },
                { key: 'customer', label: t('fields.customer'), render: (r) => r.customer?.name },
                { key: 'qty', label: t('fields.quantity_ton'), render: (r) => r.quantity_ton ?? '—' },
                { key: 'price', label: t('fields.price_per_ton'), render: (r) => formatShipmentAmount(r.price_per_ton, formatCurrency) },
                { key: 'total', label: t('fields.total_amount'), render: (r) => formatShipmentAmount(r.total_amount, formatCurrency) },
                {
                    key: 'status',
                    label: t('fields.status'),
                    render: (r) => (
                        <span className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${shipmentStatusBadgeClass(r.status)}`}>
                            {t(`shipments.statuses.${r.status}`)}
                        </span>
                    ),
                },
            ]}
        />
    );
}
