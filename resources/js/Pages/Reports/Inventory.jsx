import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import ReportExportButtons from '@/Components/Erp/ReportExportButtons';
import useTranslation from '@/hooks/useTranslation';
import { Head, Link } from '@inertiajs/react';

export default function Inventory({ rows }) {
    const { t } = useTranslation();
    const list = Array.isArray(rows) ? rows : [];

    return (
        <ErpLayout>
            <Head title={t('reports.inventory')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.inventory')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <div className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <ReportExportButtons exportType="inventory" />
            </div>
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50"><tr><th className="px-4 py-3 text-left">{t('fields.name')}</th><th className="px-4 py-3 text-left">{t('fields.sku')}</th><th className="px-4 py-3 text-left">{t('fields.unit')}</th><th className="px-4 py-3 text-left">{t('fields.current_stock')}</th><th className="px-4 py-3 text-left">{t('fields.min_stock')}</th></tr></thead>
                    <tbody className="divide-y">
                        {list.map((r) => (
                            <tr key={r.id} className={r.current_stock <= r.min_stock ? 'bg-amber-50' : ''}>
                                <td className="px-4 py-3">{r.name}</td><td className="px-4 py-3">{r.sku}</td><td className="px-4 py-3">{r.unit}</td><td className="px-4 py-3">{r.current_stock}</td><td className="px-4 py-3">{r.min_stock}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
