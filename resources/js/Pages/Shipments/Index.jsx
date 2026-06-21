import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { formatShipmentAmount, shipmentStatusBadgeClass } from '@/utils/shipmentStatus';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ shipments, filters, customers }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [status, setStatus] = useState(filters?.status || '');
    const [customerId, setCustomerId] = useState(filters?.customer_id || '');
    const list = resourceItems(shipments);

    const applyFilters = (e) => {
        e.preventDefault();
        router.get(route('shipments.index'), { status, customer_id: customerId }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('shipments.title')} />
            <FlashMessage />
            <PageHeader title={t('shipments.title')} createRoute={can('shipments.create') ? route('shipments.create') : null} createLabel={t('shipments.create')} />

            <form onSubmit={applyFilters} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
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
                <div>
                    <label className="block text-sm font-medium text-gray-700">{t('fields.customer')}</label>
                    <select className="mt-1 rounded-md border-gray-300 text-sm" value={customerId} onChange={(e) => setCustomerId(e.target.value)}>
                        <option value="">--</option>
                        {resourceItems(customers).map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                </div>
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.price_per_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((s) => (
                            <tr key={s.id} className={s.status !== 'completed' ? 'bg-amber-50/40' : ''}>
                                <td className="px-4 py-3">{s.shipment_date_shamsi}</td>
                                <td className="px-4 py-3">{s.customer?.name}</td>
                                <td className="px-4 py-3">{s.quantity_ton ?? '—'}</td>
                                <td className="px-4 py-3">{formatShipmentAmount(s.price_per_ton, formatCurrency)}</td>
                                <td className="px-4 py-3 font-medium">{formatShipmentAmount(s.total_amount, formatCurrency)}</td>
                                <td className="px-4 py-3">
                                    <span className={`inline-flex rounded-full px-2 py-0.5 text-xs font-medium ${shipmentStatusBadgeClass(s.status)}`}>
                                        {t(`shipments.statuses.${s.status}`)}
                                    </span>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('shipments.show', s.id)}
                                        editHref={can('shipments.edit') ? route('shipments.edit', s.id) : null}
                                        onDelete={can('shipments.delete') ? () => router.delete(route('shipments.destroy', s.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={shipments} /></div>
            </div>
        </ErpLayout>
    );
}
