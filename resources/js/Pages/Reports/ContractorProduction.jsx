import ReportFilterForm from '@/Components/Erp/ReportFilterForm';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatCurrency, formatNumber } from '@/utils/format';
import { resourceItems } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';

export default function ContractorProduction({ rows, filters }) {
    const { t } = useTranslation();
    const list = resourceItems(rows);

    return (
        <ErpLayout>
            <Head title={t('reports.contractor_production')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('reports.contractor_production')}</h1>
                <Link href={route('reports.index')} className="text-sm text-indigo-600 hover:underline">{t('actions.back')}</Link>
            </div>
            <ReportFilterForm reportType="contractor-production" routeName="reports.contractor-production" filters={filters} exportType="contractor-production" />
            <div className="overflow-x-auto rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.quantity_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.rate_per_ton')}</th>
                            <th className="px-4 py-3 text-left">{t('contractor_royalty.total_royalty')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {list.map((row) => (
                            <tr key={row.id}>
                                <td className="px-4 py-3">{row.production_date_shamsi}</td>
                                <td className="px-4 py-3">{formatNumber(row.quantity_ton, 3)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.rate_per_ton)}</td>
                                <td className="px-4 py-3">{formatCurrency(row.total_royalty)}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
