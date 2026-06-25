import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function DailyProduction({ rows, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.daily_production')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.daily_production')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="daily-production"
                routeName="reports.daily-production"
                filters={filters}
                exportType="daily-production"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('report_filters.created_by')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.notes')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.shipment_date_shamsi}</td>
                                <td className="px-4 py-3">{row.quantity_ton ?? '—'}</td>
                                <td className="px-4 py-3">{row.creator?.name || '—'}</td>
                                <td className="px-4 py-3">{row.notes || '—'}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
