import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { Head, router } from '@inertiajs/react';

export default function Index({ employees, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('employees.title')} />
            <FlashMessage />
            <PageHeader title={t('employees.title')} createRoute={can('employees.create') ? route('employees.create') : null} createLabel={t('employees.create')} />

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.position')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.salary')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.status')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {employees?.data?.map((e) => (
                            <tr key={e.id}>
                                <td className="px-4 py-3">{e.name}</td>
                                <td className="px-4 py-3">{e.position}</td>
                                <td className="px-4 py-3">{formatCurrency(e.salary)}</td>
                                <td className="px-4 py-3">{t(`status.${e.status}`)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('employees.show', e.id)}
                                        editHref={can('employees.edit') ? route('employees.edit', e.id) : null}
                                        onDelete={can('employees.delete') ? () => router.delete(route('employees.destroy', e.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={employees} /></div>
            </div>
        </ErpLayout>
    );
}
