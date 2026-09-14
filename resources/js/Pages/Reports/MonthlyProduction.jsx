import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency, formatNumber } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function MonthlyProduction({ rows, summary, filters }) {
    const { t } = useTranslation();
    const list = Array.isArray(rows) ? rows : [];

    return (
        <ErpLayout>
            <Head title={t('reports.monthly_production')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.monthly_production')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>

            <ReportFilterForm
                reportType="monthly-production"
                routeName="reports.monthly-production"
                filters={filters}
                exportType="monthly-production"
            />

            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('report_filters.month')}</th>
                            <th className="px-4 py-3 text-left">{t('report_filters.quantity')}</th>
                            <th className="px-4 py-3 text-left">{t('report_filters.total_tons')}</th>
                            <th className="px-4 py-3 text-left">{t('reports.total_sales')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.length === 0 && <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('messages.no_records')}</td></tr>}
                        {list.map((row) => (
                            <tr key={row.month}>
                                <td className="px-4 py-3">{row.month}</td>
                                <td className="px-4 py-3">{row.shipment_count}</td>
                                <td className="px-4 py-3">{row.total_tons}</td>
                                <td className="px-4 py-3">{formatCurrency(row.total_sales)}</td>
                            </tr>
                        ))}
                        {list.length > 0 && summary && (
                            <tr className="bg-gray-50 font-semibold">
                                <td className="px-4 py-3">{t('reports.totals')}</td>
                                <td className="px-4 py-3">{summary.shipment_count}</td>
                                <td className="px-4 py-3">{formatNumber(summary.total_tons, 3)}</td>
                                <td className="px-4 py-3">{formatCurrency(summary.total_sales)}</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
