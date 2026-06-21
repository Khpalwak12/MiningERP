import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { Head, router } from '@inertiajs/react';

export default function Index({ payments, filters, employees }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('payroll.title')} />
            <FlashMessage />
            <PageHeader title={t('payroll.title')} createRoute={can('payroll.create') ? route('payroll.create') : null} createLabel={t('payroll.create')} />

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.employee')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.payment_type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.amount')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {payments?.data?.map((p) => (
                            <tr key={p.id}>
                                <td className="px-4 py-3">{p.payment_date_shamsi}</td>
                                <td className="px-4 py-3">{p.employee?.name}</td>
                                <td className="px-4 py-3">{t(`payment_types.${p.payment_type}`)}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(p.amount)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        editHref={can('payroll.edit') ? route('payroll.edit', p.id) : null}
                                        onDelete={can('payroll.delete') ? () => router.delete(route('payroll.destroy', p.id)) : null}
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
