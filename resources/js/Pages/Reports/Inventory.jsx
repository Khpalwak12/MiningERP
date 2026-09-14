import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatNumber } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function Inventory({ rows, summary, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.inventory')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.inventory')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="inventory"
                routeName="reports.inventory"
                filters={filters}
                exportType="inventory"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.name')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.sku')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.category')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.movement_type')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={6} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((r) => (
                            <tr key={r.id}>
                                <td className="px-4 py-3">{r.movement_date_shamsi}</td>
                                <td className="px-4 py-3">{r.inventory_item?.name}</td>
                                <td className="px-4 py-3">{r.inventory_item?.sku}</td>
                                <td className="px-4 py-3">{r.inventory_item?.category || '—'}</td>
                                <td className="px-4 py-3">{t(`movement_types.${r.movement_type}`)}</td>
                                <td className="px-4 py-3">{r.quantity}</td>
                            </tr>
                        ))}
                        {list.length > 0 && summary && (
                            <tr className="bg-gray-50 font-semibold">
                                <td className="px-4 py-3">{t('reports.totals')}</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">—</td>
                                <td className="px-4 py-3">{formatNumber(summary.quantity, 3)}</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
