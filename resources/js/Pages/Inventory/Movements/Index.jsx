import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ item, movements, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const i = item.data;

    return (
        <ErpLayout>
            <Head title={t('inventory.movements')} />
            <FlashMessage />
            <PageHeader title={`${t('inventory.movements')} - ${i.name}`}>
                <Link href={route('inventory.show', i.id)} className="text-sm text-gray-600 hover:underline">{t('actions.back')}</Link>
                {can('inventory.create') && (
                    <Link href={route('inventory.movements.create', i.id)}><PrimaryButton>{t('inventory.add_movement')}</PrimaryButton></Link>
                )}
            </PageHeader>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.movement_type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.reference')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {movements?.data?.length === 0 && (
                            <tr><td colSpan="4" className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {movements?.data?.map((m) => (
                            <tr key={m.id}>
                                <td className="px-4 py-3">{m.movement_date_shamsi}</td>
                                <td className="px-4 py-3">{t(`movement_types.${m.movement_type}`)}</td>
                                <td className="px-4 py-3">{m.quantity}</td>
                                <td className="px-4 py-3">{m.reference}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={movements} /></div>
            </div>
        </ErpLayout>
    );
}
