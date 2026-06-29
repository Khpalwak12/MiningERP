import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import StatCard from '@/Components/Erp/StatCard';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { PERSONAL_CURRENCIES } from '@/config/personalAccountConfig';
import { formatMoney } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ items, filters, totals }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions, isTransactionReadOnly } = useFinancialYearMode();
    const [search, setSearch] = useState(filters?.search || '');
    const [currency, setCurrency] = useState(filters?.currency || '');
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('machinery.index'), {
            search, currency, date_from: dateFrom, date_to: dateTo,
        }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('machinery.title')} />
            <FlashMessage />
            <PageHeader
                title={t('machinery.title')}
                createRoute={canCreateTransactions && can('machinery.create') ? route('machinery.create') : null}
                createLabel={t('machinery.create')}
            />

            <div className="mb-4 grid gap-4 sm:grid-cols-2">
                <StatCard title={t('machinery.total_afn')} value={formatMoney(totals?.AFN)} color="amber" />
                <StatCard title={t('machinery.total_usd')} value={formatMoney(totals?.USD, 'USD')} color="indigo" />
            </div>

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <div>
                    <label className="block text-sm font-medium text-gray-700">{t('fields.currency')}</label>
                    <select className="mt-1 rounded-md border-gray-300 text-sm" value={currency} onChange={(e) => setCurrency(e.target.value)}>
                        <option value="">{t('actions.filter')}</option>
                        {PERSONAL_CURRENCIES.map((c) => <option key={c} value={c}>{t(`currencies.${c}`)}</option>)}
                    </select>
                </div>
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
                            <th className="px-4 py-3 text-left">{t('fields.bill_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.currency')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items?.data?.length === 0 && <tr><td colSpan={7} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {items?.data?.map((item) => (
                            <tr key={item.id}>
                                <td className="px-4 py-3">{item.purchase_date_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{item.item_name}</td>
                                <td className="px-4 py-3">{item.bill_number || '—'}</td>
                                <td className="px-4 py-3">{t(`currencies.${item.currency}`)}</td>
                                <td className="px-4 py-3">{formatMoney(item.amount, item.currency)}</td>
                                <td className="px-4 py-3">{item.description || '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={!isTransactionReadOnly && can('machinery.edit') && !item.is_locked ? route('machinery.edit', item.id) : null}
                                        onDelete={!isTransactionReadOnly && can('machinery.delete') && !item.is_locked ? () => router.delete(route('machinery.destroy', item.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={items} /></div>
            </div>
        </ErpLayout>
    );
}
