import ActionButtons from '@/Components/Erp/ActionButtons';
import ExpensesNav from '@/Components/Erp/ExpensesNav';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { resourceItems } from '@/utils/resource';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ categories, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');
    const list = resourceItems(categories);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('expense-categories.index'), { search }, { preserveState: true });
    };

    return (
        <ErpLayout>
            <Head title={t('expense_categories.title')} />
            <FlashMessage />
            <h1 className="mb-2 text-2xl font-bold">{t('expenses.title')}</h1>
            <ExpensesNav active="expense-categories.index" />
            <PageHeader
                title={t('expense_categories.title')}
                createRoute={can('expense-categories.create') ? route('expense-categories.create') : null}
                createLabel={t('expense_categories.create')}
            />

            <form onSubmit={handleSearch} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
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
                            <th className="px-4 py-3 text-left">{t('expense_categories.name_en')}</th>
                            <th className="px-4 py-3 text-left">{t('expense_categories.name_ps')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && (
                            <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {list.map((category) => (
                            <tr key={category.id}>
                                <td className="px-4 py-3 font-medium">{category.name_en}</td>
                                <td className="px-4 py-3">{category.name_ps}</td>
                                <td className="px-4 py-3">{category.description || '—'}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('expense-categories.edit') ? route('expense-categories.edit', category.id) : null}
                                        onDelete={can('expense-categories.delete') ? () => router.delete(route('expense-categories.destroy', category.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={categories} /></div>
            </div>
        </ErpLayout>
    );
}
