import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { formatCurrency } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ payments, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions, isTransactionReadOnly } = useFinancialYearMode();
    const [search, setSearch] = useState(filters?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('payments.index'), { search }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('payments.title')} />
            <FlashMessage />
            <PageHeader title={t('payments.title')} createRoute={canCreateTransactions && can('payments.create') ? route('payments.create') : null} createLabel={t('payments.create')} />

            <form onSubmit={handleSearch} className="mb-4 flex gap-2">
                <TextInput
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder={t('fields.search')}
                    className="max-w-xs"
                />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.customer')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.receipt_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.received_by')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {payments?.data?.length === 0 && (
                            <tr><td colSpan={6} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {payments?.data?.map((p) => (
                            <tr key={p.id}>
                                <td className="px-4 py-3">{p.payment_date_shamsi}</td>
                                <td className="px-4 py-3">{p.customer?.name}</td>
                                <td className="px-4 py-3">{p.receipt_number || '—'}</td>
                                <td className="px-4 py-3">{p.received_by}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(p.amount)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('payments.show', p.id)}
                                        editHref={!isTransactionReadOnly && can('payments.edit') && !p.is_locked ? route('payments.edit', p.id) : null}
                                        onDelete={!isTransactionReadOnly && can('payments.delete') && !p.is_locked ? () => router.delete(route('payments.destroy', p.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={payments} /></div>
            </div>
        </ErpLayout>
    );
}
