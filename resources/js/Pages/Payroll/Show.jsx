import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { Head } from '@inertiajs/react';

export default function Show({ payment }) {
    const { t } = useTranslation();
    const p = payment.data;

    return (
        <ErpLayout>
            <Head title={t('payroll.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('payroll.show')} #{p.id}</h1>
                <ActionButtons editHref={!p.is_locked ? route('payroll.edit', p.id) : null} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{p.payment_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.employee')}</dt><dd>{p.employee?.name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.amount')}</dt><dd className="font-bold">{formatCurrency(p.amount)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.payment_type')}</dt><dd>{t(`payment_types.${p.payment_type}`)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.period_month')}</dt><dd>{p.period_month}</dd></div>
                    {p.notes && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{p.notes}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
