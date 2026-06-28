import ActionButtons from '@/Components/Erp/ActionButtons';
import ContractorRoyaltyNav from '@/Components/Erp/ContractorRoyaltyNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import useFinancialYearMode from '@/hooks/useFinancialYearMode';
import { formatCurrency, formatNumber } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ productions, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { canCreateTransactions, isTransactionReadOnly } = useFinancialYearMode();
    const [search, setSearch] = useState(filters?.search || '');
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');
    const list = resourceItems(productions);

    return (
        <ErpLayout>
            <Head title={t('contractor_royalty.productions')} />
            <FlashMessage />
            <PageHeader
                title={t('contractor_royalty.productions')}
                createRoute={canCreateTransactions && can('contractor-royalty.create') ? route('contractor-royalty.productions.create') : null}
                createLabel={t('contractor_royalty.create_production')}
            />
            <ContractorRoyaltyNav active="contractor-royalty.productions.index" />

            <form onSubmit={(e) => {
                e.preventDefault();
                router.get(route('contractor-royalty.productions.index'), { search, date_from: dateFrom, date_to: dateTo }, { preserveState: true });
            }} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <ShamsiDateInput label={t('fields.date_from')} value={dateFrom} onChange={setDateFrom} />
                <ShamsiDateInput label={t('fields.date_to')} value={dateTo} onChange={setDateTo} />
                <PrimaryButton type="submit">{t('actions.filter')}</PrimaryButton>
            </form>

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.rate_per_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('contractor_royalty.total_royalty')}</th>
                            <th className="px-4 py-3 text-left">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={5} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.production_date_shamsi}</td>
                                <td className="px-4 py-3">{formatNumber(row.quantity_ton, 3)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.rate_per_ton)}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(row.total_royalty)}</td>
                                <td className="px-4 py-3">
                                    <ActionButtons
                                        viewHref={route('contractor-royalty.productions.show', row.id)}
                                        editHref={!isTransactionReadOnly && can('contractor-royalty.edit') && !row.is_locked ? route('contractor-royalty.productions.edit', row.id) : null}
                                        onDelete={!isTransactionReadOnly && can('contractor-royalty.delete') && !row.is_locked ? () => router.delete(route('contractor-royalty.productions.destroy', row.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            <Pagination links={productions?.links} />
        </ErpLayout>
    );
}
