import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head } from '@inertiajs/react';

export default function Show({ payment }) {
    const { t } = useTranslation();
    const p = resourceData(payment);

    return (
        <ErpLayout>
            <Head title={t('payments.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('payments.show')} #{p.id}</h1>
                <ActionButtons editHref={route('payments.edit', p.id)} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{p.payment_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.customer')}</dt><dd>{p.customer?.name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.amount')}</dt><dd className="font-bold">{formatCurrency(p.amount)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.receipt_number')}</dt><dd>{p.receipt_number || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.received_by')}</dt><dd>{p.received_by}</dd></div>
                    {p.notes && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{p.notes}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
