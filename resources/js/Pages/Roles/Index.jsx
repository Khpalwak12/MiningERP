import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, router } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ roles, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('roles.title')} />
            <FlashMessage />
            <PageHeader title={t('roles.title')} createRoute={can('roles.create') ? route('roles.create') : null} createLabel={t('roles.create')} />

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.permissions')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {roles?.data?.map((r) => (
                            <tr key={r.id}>
                                <td className="px-4 py-3 font-medium">{r.name}</td>
                                <td className="px-4 py-3 text-gray-600">{r.permissions?.length || 0} permissions</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('roles.edit') ? route('roles.edit', r.id) : null}
                                        onDelete={can('roles.delete') && r.name !== 'Super Admin'
                                            ? () => router.delete(route('roles.destroy', r.id))
                                            : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={roles} /></div>
            </div>
        </ErpLayout>
    );
}
