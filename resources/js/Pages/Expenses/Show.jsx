import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head } from '@inertiajs/react';

export default function Show({ expense }) {
    const { t } = useTranslation();
    const e = resourceData(expense);

    return (
        <ErpLayout>
            <Head title={t('expenses.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('expenses.show')} #{e.id}</h1>
                <ActionButtons editHref={!e.is_locked ? route('expenses.edit', e.id) : null} />
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.date')}</dt><dd>{e.expense_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.category')}</dt><dd>{e.category?.name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.subcategory')}</dt><dd>{e.subcategory || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.bill_number')}</dt><dd>{e.bill_number || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.amount')}</dt><dd className="font-bold">{formatCurrency(e.amount)}</dd></div>
                    {e.description && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.description')}</dt><dd>{e.description}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
