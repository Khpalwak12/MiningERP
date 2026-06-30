import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { formatShipmentAmount, isShipmentCompleted, shipmentStatusBadgeClass } from '@/utils/shipmentStatus';
import { Head, Link } from '@inertiajs/react';

export default function Sales({ rows, filters, customers, selectedCustomer, mineTypes }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.sales')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.sales')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="sales"
                routeName="reports.sales"
                filters={filters}
                exportType="sales"
                showCustomer
                showMineType
                showStatus
                lookups={{ customers, selectedCustomer, mineTypes }}
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.mine_type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.price_per_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.id} className={row.status && !isShipmentCompleted(row.status) ? 'bg-amber-50/40' : ''}>
                                <td className="px-4 py-3">{row.shipment_date_shamsi || row.shipment_date}</td>
                                <td className="px-4 py-3">{row.customer?.name}</td>
                                <td className="px-4 py-3">{row.mine_type?.name || '—'}</td>
                                <td className="px-4 py-3">{row.quantity_ton ?? '—'}</td>
                                <td className="px-4 py-3">{formatShipmentAmount(row.price_per_ton, formatCurrency)}</td>
                                <td className="px-4 py-3">{formatShipmentAmount(row.total_amount, formatCurrency)}</td>
                                <td className="px-4 py-3">
                                    <span className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${shipmentStatusBadgeClass(row.status)}`}>
                                        {t(`shipments.statuses.${row.status}`)}
                                    </span>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
