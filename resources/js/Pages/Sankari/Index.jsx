import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { formatCurrency } from '@/utils/format';
import { Head, router } from '@inertiajs/react';

export default function Index({ sales, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('sankari.title')} />
            <FlashMessage />
            <PageHeader title={t('sankari.title')} createRoute={can('sankari.create') ? route('sankari.create') : null} createLabel={t('sankari.create')} />

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.truck_count')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.price_per_truck')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.total_amount')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.payment_type')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {sales?.data?.length === 0 && (
                            <tr><td colSpan="6" className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {sales?.data?.map((s) => (
                            <tr key={s.id}>
                                <td className="px-4 py-3">{s.sale_date_shamsi}</td>
                                <td className="px-4 py-3">{s.truck_count}</td>
                                <td className="px-4 py-3">{formatCurrency(s.price_per_truck)}</td>
                                <td className="px-4 py-3 font-medium">{formatCurrency(s.total_amount)}</td>
                                <td className="px-4 py-3">{t(`payment_types.${s.payment_type}`)}</td>
                                <td className="px-4 py-3 text-right">
                                    <ActionButtons
                                        viewHref={route('sankari.show', s.id)}
                                        editHref={can('sankari.edit') && !s.is_locked ? route('sankari.edit', s.id) : null}
                                        onDelete={can('sankari.delete') && !s.is_locked ? () => router.delete(route('sankari.destroy', s.id)) : null}
                                    />
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={sales} /></div>
            </div>
        </ErpLayout>
    );
}
