import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ customers, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('customers.index'), { search }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('customers.title')} />
            <FlashMessage />
            <PageHeader
                title={t('customers.title')}
                createRoute={can('customers.create') ? route('customers.create') : null}
                createLabel={t('customers.create')}
            />

            <form onSubmit={handleSearch} className="mb-4 flex gap-2">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full divide-y divide-gray-200 text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left font-medium text-gray-500">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left font-medium text-gray-500">{t('fields.phone')}</th>
                            <th className="px-4 py-3 text-left font-medium text-gray-500">{t('customers.total_sales')}</th>
                            <th className="px-4 py-3 text-left font-medium text-gray-500">{t('customers.outstanding_balance')}</th>
                            <th className="px-4 py-3 text-left font-medium text-gray-500">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-right font-medium text-gray-500">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                        {customers?.data?.length === 0 && (
                            <tr><td colSpan="6" className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {customers?.data?.map((c) => (
                            <tr key={c.id}>
                                <td className="px-4 py-3 font-medium">{c.name}</td>
                                <td className="px-4 py-3">{c.phone}</td>
                                <td className="px-4 py-3">{formatCurrency(c.total_sales)}</td>
                                <td className="px-4 py-3 text-amber-700 font-medium">{formatCurrency(c.outstanding_balance)}</td>
                                <td className="px-4 py-3">{t(`status.${c.status}`)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('customers.show', c.id)}
                                        editHref={can('customers.edit') ? route('customers.edit', c.id) : null}
                                        onDelete={can('customers.delete') ? () => router.delete(route('customers.destroy', c.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={customers} /></div>
            </div>
        </ErpLayout>
    );
}
