import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ items, filters }) {
    const { t } = useTranslation();
    const { can } = usePermission();

    return (
        <ErpLayout>
            <Head title={t('inventory.title')} />
            <FlashMessage />
            <PageHeader title={t('inventory.title')} createRoute={can('inventory.create') ? route('inventory.create') : null} createLabel={t('inventory.create')} />

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.sku')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.unit')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.current_stock')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.min_stock')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {items?.data?.length === 0 && (
                            <tr><td colSpan="6" className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>
                        )}
                        {items?.data?.map((item) => (
                            <tr key={item.id} className={item.is_low_stock ? 'bg-amber-50' : ''}>
                                <td className="px-4 py-3 font-medium">{item.name}</td>
                                <td className="px-4 py-3">{item.sku}</td>
                                <td className="px-4 py-3">{item.unit}</td>
                                <td className="px-4 py-3">{item.current_stock}</td>
                                <td className="px-4 py-3">{item.min_stock}</td>
                                <td className="px-4 py-3 text-right">
                                    <div className="inline-flex items-center justify-end gap-2">
                                        <Link href={route('inventory.movements.index', item.id)} className="text-xs text-gray-600 hover:underline">{t('inventory.movements')}</Link>
                                        <ActionButtons
                                            viewHref={route('inventory.show', item.id)}
                                            editHref={can('inventory.edit') ? route('inventory.edit', item.id) : null}
                                            onDelete={can('inventory.delete') ? () => router.delete(route('inventory.destroy', item.id)) : null}
                                        />
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4"><Pagination paginator={items} /></div>
            </div>
        </ErpLayout>
    );
}
