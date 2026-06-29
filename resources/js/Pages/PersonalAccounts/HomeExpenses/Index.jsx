import ActionButtons from '@/Components/Erp/ActionButtons';
import PersonalAccountsNav from '@/Components/Erp/PersonalAccountsNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { formatMoney } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ expenses, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions, isTransactionReadOnly } = useFinancialYearMode();
    const [search, setSearch] = useState(filters?.search || '');
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('personal-accounts.home-expenses.index'), {
            search, date_from: dateFrom, date_to: dateTo,
        }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('personal_accounts.home_expenses')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('personal_accounts.title')}</h1>
            <PersonalAccountsNav active="personal-accounts.home-expenses.index" />
            <PageHeader
                title={t('personal_accounts.home_expenses')}
                createRoute={canCreateTransactions && can('personal-accounts.create') ? route('personal-accounts.home-expenses.create') : null}
                createLabel={t('personal_accounts.create_home_expense')}
            />

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {expenses?.data?.length === 0 && <tr><td colSpan={5} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {expenses?.data?.map((e) => (
                            <tr key={e.id}>
                                <td className="px-4 py-3">{e.expense_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{e.item_name}</td>
                                <td className="px-4 py-3">{formatMoney(e.amount)}</td>
                                <td className="px-4 py-3">{e.description || '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={!isTransactionReadOnly && can('personal-accounts.edit') && !e.is_locked ? route('personal-accounts.home-expenses.edit', e.id) : null}
                                        onDelete={!isTransactionReadOnly && can('personal-accounts.delete') && !e.is_locked ? () => router.delete(route('personal-accounts.home-expenses.destroy', e.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={expenses} /></div>
            </div>
        </ErpLayout>
    );
}
