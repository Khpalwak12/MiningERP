import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { formatShipmentAmount, shipmentStatusBadgeClass } from '@/utils/shipmentStatus';
import { Head } from '@inertiajs/react';

export default function Show({ shipment }) {
    const { t } = useTranslation();
    const s = resourceData(shipment);

    return (
        <ErpLayout>
            <Head title={t('shipments.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('shipments.show')} #{s.id}</h1>
                <ActionButtons editHref={!s.is_locked ? route('shipments.edit', s.id) : null} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <div className="mb-4">
                    <span className={`inline-flex rounded-full px-3 py-1 text-xs font-medium ${shipmentStatusBadgeClass(s.status)}`}>
                        {t(`shipments.statuses.${s.status}`)}
                    </span>
                </div>
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{s.shipment_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.customer')}</dt><dd>{s.customer?.name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.mine_type')}</dt><dd>{s.mine_type?.name || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.stone_type')}</dt><dd>{s.stone_type?.name || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.driver_name')}</dt><dd>{s.driver_name || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.quantity_ton')}</dt><dd>{s.quantity_ton ?? '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.price_per_ton')}</dt><dd>{formatShipmentAmount(s.price_per_ton, formatCurrency)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.total_amount')}</dt><dd className="font-bold">{formatShipmentAmount(s.total_amount, formatCurrency)}</dd></div>
                    {s.notes && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{s.notes}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
