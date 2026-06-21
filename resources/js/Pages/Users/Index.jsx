import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ users, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const [search, setSearch] = useState(filters?.search || '');

    return (
        <ErpLayout>
            <Head title={t('users.title')} />
            <FlashMessage />
            <PageHeader title={t('users.title')} createRoute={can('users.create') ? route('users.create') : null} createLabel={t('users.create')} />

            <form onSubmit={(e) => { e.preventDefault(); router.get(route('users.index'), { search }, { preserveState: true }); }} className="mb-4 flex gap-2">
                <TextInput value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('fields.search')} className="max-w-xs" />
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.email')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.roles')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {users?.data?.map((u) => (
                            <tr key={u.id}>
                                <td className="px-4 py-3">{u.name}</td>
                                <td className="px-4 py-3">{u.email}</td>
                                <td className="px-4 py-3">{u.roles?.join(', ')}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('users.edit') ? route('users.edit', u.id) : null}
                                        onDelete={can('users.delete') ? () => router.delete(route('users.destroy', u.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={users} /></div>
            </div>
        </ErpLayout>
    );
}
