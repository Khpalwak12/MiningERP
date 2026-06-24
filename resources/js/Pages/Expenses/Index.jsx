import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import ShamsiDateInput from '@/Components/Erp/ShamsiDateInput';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ expenses, filters, categories }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');
    const [categoryId, setCategoryId] = useState(filters?.expense_category_id || '');
    const [dateFrom, setDateFrom] = useState(filters?.date_from || '');
    const [dateTo, setDateTo] = useState(filters?.date_to || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('expenses.index'), {
            search,
            expense_category_id: categoryId,
            date_from: dateFrom,
            date_to: dateTo,
        }, { preserveState: true });
    };

    const categoryList = resourceItems(categories);

    return (
        <ErpLayout>
            <Head title={t('expenses.title')} />
            <FlashMessage />
            <PageHeader title={t('expenses.title')} createRoute={can('expenses.create') ? route('expenses.create') : null} createLabel={t('expenses.create')} />

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <TextInput
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    placeholder={t('fields.search')}
                    className="max-w-xs"
                />
                <div>
                    <label className="block text-sm font-medium text-gray-700">{t('fields.category')}</label>
                    <select className="mt-1 rounded-md border-gray-300 text-sm" value={categoryId} onChange={(e) => setCategoryId(e.target.value)}>
                        <option value="">{t('actions.filter')} — {t('fields.category')}</option>
                        {categoryList.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
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
                            <th className="px-4 py-3 text-left">{t('fields.category')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.subcategory')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.bill_number')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {expenses?.data?.length === 0 && (
                            <tr><td colSpan={6} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {expenses?.data?.map((e) => (
                            <tr key={e.id}>
                                <td className="px-4 py-3">{e.expense_date_shamsi}</td>
                                <td className="px-4 py-3">{e.category?.name}</td>
                                <td className="px-4 py-3">{e.subcategory || '—'}</td>
                                <td className="px-4 py-3">{e.bill_number || '—'}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(e.amount)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('expenses.show', e.id)}
                                        editHref={can('expenses.edit') && !e.is_locked ? route('expenses.edit', e.id) : null}
                                        onDelete={can('expenses.delete') && !e.is_locked ? () => router.delete(route('expenses.destroy', e.id)) : null}
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
