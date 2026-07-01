import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head } from '@inertiajs/react';
import { resourceData } from '@/utils/resource';

export default function Show({ sale }) {
    const { t } = useTranslation();
    const s = resourceData(sale);

    return (
        <ErpLayout>
            <Head title={t('sankari.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('sankari.show')} #{s.id}</h1>
                <ActionButtons editHref={!s.is_locked ? route('sankari.edit', s.id) : null} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{s.sale_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.truck_count')}</dt><dd>{s.truck_count}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.price_per_truck')}</dt><dd>{formatCurrency(s.price_per_truck)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.subtotal')}</dt><dd>{formatCurrency(s.subtotal)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.discount')}</dt><dd>{formatCurrency(s.discount)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.total_amount')}</dt><dd className="font-bold">{formatCurrency(s.total_amount)}</dd></div>
                    {s.notes && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{s.notes}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
